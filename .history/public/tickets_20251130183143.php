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

    /**
     * Crear un ticket
     */
    public function crear(array $data): bool
    {
        $sql = "INSERT INTO tickets 
                (colaborador_id, tipo_ticket_id, titulo, descripcion, prioridad, 
                 creado_por_usuario_id, ip_origen)
                VALUES
                (:colaborador_id, :tipo_ticket_id, :titulo, :descripcion, :prioridad,
                 :creado_por_usuario_id, :ip_origen)";

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

    /**
     * Listar tickets con join a colaborador y tipo
     */
    public function listarTodos(int $limite = 20, int $offset = 0): array
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
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Contar todos los tickets (para la paginación)
     */
    public function contarTodos(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM tickets";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();

        return (int)($row['total'] ?? 0);
    }

    /**
     * Obtener un ticket por ID
     */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT t.*,
                       c.primer_nombre, c.primer_apellido,
                       tt.nombre AS tipo_nombre
                FROM tickets t
                JOIN colaboradores c ON t.colaborador_id = c.id
                JOIN tipos_ticket tt ON t.tipo_ticket_id = tt.id
                WHERE t.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Actualizar estado, solución y agente (para cuando se resuelve/cierra)
     */
    public function actualizarEstadoYSolucion(
        int $id,
        string $estado,
        string $solucion,
        int $agenteId
    ): bool {
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
            ':id'        => $id,
        ]);
    }

    /**
     * Listado para reportes con tiempo de respuesta
     */
    public function listarParaReporte(array $filtros = []): array
    {
        $where  = [];
        $params = [];

        if (!empty($filtros['estado'])) {
            $where[] = "t.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if (!empty($filtros['tipo_ticket_id'])) {
            $where[] = "t.tipo_ticket_id = :tipo_ticket_id";
            $params[':tipo_ticket_id'] = $filtros['tipo_ticket_id'];
        }

        if (!empty($filtros['agente_id'])) {
            $where[] = "t.agente_id = :agente_id";
            $params[':agente_id'] = $filtros['agente_id'];
        }

        $sqlWhere = '';
        if (!empty($where)) {
            $sqlWhere = 'WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT
                    t.id,
                    t.titulo,
                    t.estado,
                    t.prioridad,
                    t.fecha_creacion,
                    t.fecha_respuesta,
                    t.fecha_cierre,
                    TIMESTAMPDIFF(MINUTE, t.fecha_creacion, t.fecha_respuesta) AS minutos_respuesta,
                    tt.nombre AS tipo_nombre,
                    u.nombre AS agente_nombre
                FROM tickets t
                JOIN tipos_ticket tt ON t.tipo_ticket_id = tt.id
                LEFT JOIN usuarios u ON t.agente_id = u.id
                $sqlWhere
                ORDER BY t.fecha_creacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
