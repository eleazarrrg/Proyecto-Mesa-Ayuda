<?php
// app/models/Rol.php
require_once __DIR__ . '/../core/Database.php';

class Rol
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listarTodos(): array
    {
        $sql = "SELECT * FROM roles ORDER BY id";
        return $this->db->query($sql)->fetchAll();
    }

    public function contarTodos(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM roles";
        $row = $this->db->query($sql)->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM roles WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function crear(array $data): bool
    {
        $sql = "INSERT INTO roles (nombre, alcance)
                VALUES (:nombre, :alcance)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'  => $data['nombre'],
            ':alcance' => $data['alcance'],
        ]);
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE roles
                SET nombre = :nombre,
                    alcance = :alcance
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'  => $data['nombre'],
            ':alcance' => $data['alcance'],
            ':id'      => $id,
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM roles WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
