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

    public function listarTodos(int $limite = 20, int $offset = 0): array
    {
        $sql = "SELECT u.*, r.nombre AS rol_nombre
                FROM usuarios u
                LEFT JOIN roles r ON u.rol_id = r.id
                ORDER BY u.nombre
                LIMIT :limite OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarTodos(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM usuarios";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function crear(array $data): bool
    {
        $sql = "INSERT INTO usuarios
                (nombre, username, email, password_hash, rol_id, activo)
                VALUES
                (:nombre, :username, :email, :password_hash, :rol_id, :activo)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'        => $data['nombre'],
            ':username'      => $data['username'],
            ':email'         => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':rol_id'        => $data['rol_id'],
            ':activo'        => $data['activo'],
        ]);
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE usuarios
                SET nombre = :nombre,
                    username = :username,
                    email = :email,
                    rol_id = :rol_id,
                    activo = :activo
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'   => $data['nombre'],
            ':username' => $data['username'],
            ':email'    => $data['email'],
            ':rol_id'   => $data['rol_id'],
            ':activo'   => $data['activo'],
            ':id'       => $id,
        ]);
    }

    public function actualizarPassword(int $id, string $nuevoHash): bool
    {
        $sql = "UPDATE usuarios SET password_hash = :password_hash WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password_hash' => $nuevoHash,
            ':id'            => $id,
        ]);
    }

    /**
     * Eliminamos lógicamente: activo = 0
     */
    public function eliminar(int $id): bool
    {
        $sql = "UPDATE usuarios SET activo = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
