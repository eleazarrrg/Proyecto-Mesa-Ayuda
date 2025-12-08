<?php
// app/models/Colaborador.php
require_once __DIR__ . '/../core/Database.php';

class Colaborador
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listarTodos($limite = 50, $offset = 0)
    {
        $sql = "SELECT * FROM colaboradores
                ORDER BY primer_nombre, primer_apellido
                LIMIT :limite OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarTodos()
    {
        $sql = "SELECT COUNT(*) AS total FROM colaboradores";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();
        return (int)$row['total'];
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM colaboradores WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear(array $data): bool
    {
        $sql = "INSERT INTO colaboradores
                (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
                sexo, fecha_nacimiento, identificacion, email, telefono, tipo, foto_perfil)
                VALUES
                (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido,
                :sexo, :fecha_nacimiento, :identificacion, :email, :telefono, :tipo, :foto_perfil)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':primer_nombre'    => $data['primer_nombre'],
            ':segundo_nombre'   => $data['segundo_nombre'],
            ':primer_apellido'  => $data['primer_apellido'],
            ':segundo_apellido' => $data['segundo_apellido'],
            ':sexo'             => $data['sexo'],
            ':fecha_nacimiento' => $data['fecha_nacimiento'],
            ':identificacion'   => $data['identificacion'],
            ':email'            => $data['email'],
            ':telefono'         => $data['telefono'],
            ':tipo'             => $data['tipo'],
            ':foto_perfil'      => $data['foto_perfil'],
        ]);
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE colaboradores
                SET primer_nombre    = :primer_nombre,
                    segundo_nombre   = :segundo_nombre,
                    primer_apellido  = :primer_apellido,
                    segundo_apellido = :segundo_apellido,
                    sexo             = :sexo,
                    fecha_nacimiento = :fecha_nacimiento,
                    identificacion   = :identificacion,
                    email            = :email,
                    telefono         = :telefono,
                    tipo             = :tipo,
                    foto_perfil      = :foto_perfil
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':primer_nombre'    => $data['primer_nombre'],
            ':segundo_nombre'   => $data['segundo_nombre'],
            ':primer_apellido'  => $data['primer_apellido'],
            ':segundo_apellido' => $data['segundo_apellido'],
            ':sexo'             => $data['sexo'],
            ':fecha_nacimiento' => $data['fecha_nacimiento'],
            ':identificacion'   => $data['identificacion'],
            ':email'            => $data['email'],
            ':telefono'         => $data['telefono'],
            ':tipo'             => $data['tipo'],
            ':foto_perfil'      => $data['foto_perfil'],
            ':id'               => $id,
        ]);
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM colaboradores WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
