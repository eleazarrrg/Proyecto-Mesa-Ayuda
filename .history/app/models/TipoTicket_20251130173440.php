<?php
// app/models/TipoTicket.php
require_once __DIR__ . '/../core/Database.php';

class TipoTicket
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listarTodos()
    {
        $sql = "SELECT * FROM tipos_ticket ORDER BY nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM tipos_ticket WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear($data)
    {
        $sql = "INSERT INTO tipos_ticket (nombre, categoria)
                VALUES (:nombre, :categoria)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':categoria' => $data['categoria'],
        ]);
    }

    public function actualizar($id, $data)
    {
        $sql = "UPDATE tipos_ticket
                SET nombre = :nombre,
                    categoria = :categoria
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':categoria' => $data['categoria'],
            ':id' => $id,
        ]);
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM tipos_ticket WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
