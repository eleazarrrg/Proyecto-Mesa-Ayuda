<?php
// app/models/Usuario.php
require_once __DIR__ . '/../core/Database.php';

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function actualizarPassword($id, $nuevoHash)
    {
        $sql = "UPDATE usuarios SET password_hash = :password_hash WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password_hash' => $nuevoHash,
            ':id' => $id
        ]);
    }
}
