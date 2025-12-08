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
     * Crear un ticket desde el formulario del estudiante.
     * $data:
     *  estudiante_id, tipo_ticket_id, titulo, descripcion,
     *  prioridad, creado_por_usuario_id, ip_origen
     */
    public function crear(array $data): bool
    {
        $sql = "INSERT INTO tickets
                (estudiante_id, tipo_ticket_id, titulo, descripcion,
                 prioridad, creado_por_usuario_id, ip_origen)
                VALUES
                (:estudiante_id, :tipo_ticket_id, :titulo, :descripcion,
                 :prioridad, :creado_por_usuario_id, :ip_origen)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estudiante_id'        => $data['estudiante_id'],
            ':tipo_ticket_id'       => $data['tipo_ticket_id'],
            ':titulo'               => $data['titulo'],
            ':descripcion'          => $data['descripcion'],
            ':prioridad'            => $data['prioridad'],
            ':creado_por_usuario_id'=> $data['creado_por_usuario_id'],
            ':ip_origen'            => $data['ip_origen'],
        ]);
    }

    /**
     * Listado general (para admin/agente), con filtro opcional de estado.
     */
    public function listarTodos(int $limite = 10, int $offset = 0, ?string $estado = null): array
    {
        $sql = "SELECT t.*,
                       e.primer_nombre,
                       e.primer_apellido,
                       e.identificacion,
                       tt.nombre AS tipo_nombre
                FROM tickets t
                JOIN estudiantes e ON t.estudiante_id = e.id
                JOIN tipos_ticket tt ON t.tipo_ticket_id = tt.id";

        $params = [];

        if ($estado !== null) {
            $sql .= " WHERE t.estado = :estado";
            $params[':estado'] = $estado;
        }

        $sql .= " ORDER BY t.fecha_creacion ASC
                  LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarTodos(?string $estado = null): int
    {
        $sql = "SELECT COUNT(*) AS total FROM tickets";
        $params = [];

        if ($estado !== null) {
            $sql .= " WHERE estado = :estado";
            $params[':estado'] = $estado;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Listado filtrado por usuario creador (para estudiante),
     * con filtro opcional de estado.
     */
    public function listarPorCreador(
        int $usuarioId,
        int $limite = 10,
        int $offset = 0,
        ?string $estado = null
    ): array {
        $sql = "SELECT t.*,
                       e.primer_nombre,
                       e.primer_apellido,
                       e.identificacion,
                       tt.nombre AS tipo_nombre
                FROM tickets t
                JOIN estudiantes e ON t.estudiante_id = e.id
                JOIN tipos_ticket tt ON t.tipo_ticket_id = tt.id
                WHERE t.creado_por_usuario_id = :usuario_id";

        $params = [
            ':usuario_id' => $usuarioId,
        ];

        if ($estado !== null) {
            $sql .= " AND t.estado = :estado";
            $params[':estado'] = $estado;
        }

        $sql .= " ORDER BY t.fecha_creacion ASC
                  LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarPorCreador(int $usuarioId, ?string $estado = null): int
    {
        $sql = "SELECT COUNT(*) AS total
                FROM tickets
                WHERE creado_por_usuario_id = :usuario_id";
        $params = [
            ':usuario_id' => $usuarioId,
        ];

        if ($estado !== null) {
            $sql .= " AND estado = :estado";
            $params[':estado'] = $estado;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Obtener detalle de un ticket
     */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT t.*,
                       e.primer_nombre,
                       e.primer_apellido,
                       e.identificacion,
                       tt.nombre AS tipo_nombre
                FROM tickets t
                JOIN estudiantes e ON t.estudiante_id = e.id
                JOIN tipos_ticket tt ON t.tipo_ticket_id = tt.id
                WHERE t.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Actualizar gestión del ticket (solo agente)
     * Cambia estado, prioridad, solución y asigna agente/fechas.
     */
    public function actualizarGestion(
        int $id,
        string $estado,
        string $prioridad,
        ?string $solucion,
        ?int $agenteId
    ): bool {
        // Construimos dinámicamente la parte SET
        $campos = [
            "estado = :estado",
            "prioridad = :prioridad",
            "solucion = :solucion",
        ];
        $params = [
            ':estado'    => $estado,
            ':prioridad' => $prioridad,
            ':solucion'  => $solucion,
            ':id'        => $id,
        ];

        if ($agenteId !== null) {
            $campos[]             = "agente_id = :agente_id";
            $campos[]             = "fecha_asignacion = IFNULL(fecha_asignacion, NOW())";
            $params[':agente_id'] = $agenteId;
        }

        if ($estado === 'EN_PROCESO') {
            $campos[] = "fecha_respuesta = IFNULL(fecha_respuesta, NOW())";
        } elseif ($estado === 'CULMINADA') {
            $campos[] = "fecha_respuesta = IFNULL(fecha_respuesta, NOW())";
            $campos[] = "fecha_cierre   = IFNULL(fecha_cierre, NOW())";
        }

        $sql = "UPDATE tickets SET " . implode(', ', $campos) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Listar datos para reportes (filtros: estado, tipo_ticket_id, agente_id)
     * Incluye categoría, nombre del agente y minutos_respuesta.
     */
    public function listarParaReporte(array $filtros = []): array
    {
        $sql = "SELECT
                    t.id,
                    t.titulo,
                    t.estado,
                    t.prioridad,
                    t.fecha_creacion,
                    t.fecha_respuesta,
                    tt.nombre    AS tipo_nombre,
                    tt.categoria AS categoria,
                    u.nombre     AS agente_nombre,
                    TIMESTAMPDIFF(MINUTE, t.fecha_creacion, t.fecha_respuesta) AS minutos_respuesta
                FROM tickets t
                JOIN tipos_ticket tt ON t.tipo_ticket_id = tt.id
                LEFT JOIN usuarios u ON t.agente_id = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['estado'])) {
            $sql .= " AND t.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }
        if (!empty($filtros['tipo_ticket_id'])) {
            $sql .= " AND t.tipo_ticket_id = :tipo_ticket_id";
            $params[':tipo_ticket_id'] = $filtros['tipo_ticket_id'];
        }
        if (!empty($filtros['agente_id'])) {
            $sql .= " AND t.agente_id = :agente_id";
            $params[':agente_id'] = $filtros['agente_id'];
        }

        $sql .= " ORDER BY t.fecha_creacion ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
