<?php
// app/models/Eestudiantesstudiante.php
require_once __DIR__ . '/../core/Database.php';

class Estudiante
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listarTodos(int $limite = 50, int $offset = 0): array
    {
        $sql = "SELECT * FROM estudiantes
                ORDER BY primer_apellido, primer_nombre
                LIMIT :limite OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarTodos(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM estudiantes";
        $row = $this->db->query($sql)->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM estudiantes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function obtenerPorEmail(string $email): ?array
    {
        $sql = "SELECT * FROM estudiantes WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Crea un estudiantes y devuelve true/false.
     * $data debe tener:
     *  primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
     *  sexo, fecha_nacimiento, identificacion, email, telefono, tipo, foto_perfil
     */
    public function crear(array $data): bool
    {
        $sql = "INSERT INTO estudiantes
                (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
                 sexo, fecha_nacimiento, foto_perfil, identificacion, email, telefono, tipo)
                VALUES
                (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido,
                 :sexo, :fecha_nacimiento, :foto_perfil, :identificacion, :email, :telefono, :tipo)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':primer_nombre'   => $data['primer_nombre'],
            ':segundo_nombre'  => $data['segundo_nombre'],
            ':primer_apellido' => $data['primer_apellido'],
            ':segundo_apellido'=> $data['segundo_apellido'],
            ':sexo'            => $data['sexo'],
            ':fecha_nacimiento'=> $data['fecha_nacimiento'],
            ':foto_perfil'     => $data['foto_perfil'],
            ':identificacion'  => $data['identificacion'],
            ':email'           => $data['email'],
            ':telefono'        => $data['telefono'],
            ':tipo'            => $data['tipo'],
        ]);
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE estudiantes
                SET primer_nombre = :primer_nombre,
                    segundo_nombre = :segundo_nombre,
                    primer_apellido = :primer_apellido,
                    segundo_apellido = :segundo_apellido,
                    sexo = :sexo,
                    fecha_nacimiento = :fecha_nacimiento,
                    foto_perfil = :foto_perfil,
                    identificacion = :identificacion,
                    email = :email,
                    telefono = :telefono,
                    tipo = :tipo
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':primer_nombre'   => $data['primer_nombre'],
            ':segundo_nombre'  => $data['segundo_nombre'],
            ':primer_apellido' => $data['primer_apellido'],
            ':segundo_apellido'=> $data['segundo_apellido'],
            ':sexo'            => $data['sexo'],
            ':fecha_nacimiento'=> $data['fecha_nacimiento'],
            ':foto_perfil'     => $data['foto_perfil'],
            ':identificacion'  => $data['identificacion'],
            ':email'           => $data['email'],
            ':telefono'        => $data['telefono'],
            ':tipo'            => $data['tipo'],
            ':id'              => $id,
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM estudiantes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
