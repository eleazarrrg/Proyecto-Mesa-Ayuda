<?php
// app/models/Ticket.php
require_once __DIR__ . '/../core/Database.php';

class Ticket
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function crear($data)
    {
        $sql = "INSERT INTO tickets 
            (colaborador_id, tipo_ticket_id, titulo, descripcion, prioridad, creado_por_usuario_id, ip_origen)
            VALUES
            (:colaborador_id, :tipo_ticket_id, :titulo, :descripcion, :prioridad, :creado_por_usuario_id, :ip_origen)";
        
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':colaborador_id'        => $data['colaborador_id'],
            ':tipo_ticket_id'        => $data['tipo_ticket_id'],
            ':titulo'                => $data['titulo'],
            ':descripcion'           => $data['descripcion'],
            ':prioridad'             => $data['prioridad'],
            ':creado_por_usuario_id' => $data['creado_por_usuario_id'],
            ':ip_origen'             => $data['ip_origen'],
        ]);
    }

    public function listarTodos($limite = 20, $offset = 0)
    {
        $sql = "SELECT t.*, 
                       c.primer_nombre, c.primer_apellido,
                       tt.nombre AS tipo_nombre
                FROM tickets t
                JOIN colaboradores c ON t.colaborador_id = c.id
                JOIN tipos_ticket tt ON t.tipo_ticket_id = tt.id
                ORDER BY t.fecha_creacion DESC
                LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function contarTodos()
    {
        $sql = "SELECT COUNT(*) AS total FROM tickets";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();
        return (int)$row['total'];
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM tickets WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function actualizarEstadoYSolucion($id, $estado, $solucion, $agenteId)
    {
        $sql = "UPDATE tickets
                SET estado = :estado,
                    solucion = :solucion,
                    agente_id = :agente_id,
                    fecha_respuesta = IF(fecha_respuesta IS NULL, NOW(), fecha_respuesta),
                    fecha_cierre = CASE 
                      WHEN :estado = 'CERRADO' THEN NOW()
                      ELSE fecha_cierre
                    END
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado'    => $estado,
            ':solucion'  => $solucion,
            ':agente_id' => $agenteId,
            ':id'        => $id
        ]);
    }
}
