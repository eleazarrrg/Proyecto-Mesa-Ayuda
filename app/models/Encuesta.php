<?php
// app/models/Encuesta.php
require_once __DIR__ . '/../core/Database.php';

class Encuesta
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function crearOActualizar($ticketId, $nivel, $comentario)
    {
        // Ver si ya existe encuesta para este ticket
        $sql = "SELECT id FROM encuestas_satisfaccion WHERE ticket_id = :ticket_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ticket_id' => $ticketId]);
        $existe = $stmt->fetch();

        if ($existe) {
            $sqlUpdate = "UPDATE encuestas_satisfaccion
                          SET nivel = :nivel, comentario = :comentario, fecha = NOW()
                          WHERE ticket_id = :ticket_id";
            $stmt2 = $this->db->prepare($sqlUpdate);
            return $stmt2->execute([
                ':nivel' => $nivel,
                ':comentario' => $comentario,
                ':ticket_id' => $ticketId
            ]);
        } else {
            $sqlInsert = "INSERT INTO encuestas_satisfaccion (ticket_id, nivel, comentario)
                          VALUES (:ticket_id, :nivel, :comentario)";
            $stmt2 = $this->db->prepare($sqlInsert);
            return $stmt2->execute([
                ':ticket_id' => $ticketId,
                ':nivel' => $nivel,
                ':comentario' => $comentario
            ]);
        }
    }

    public function obtenerPorTicket($ticketId)
    {
        $sql = "SELECT * FROM encuestas_satisfaccion WHERE ticket_id = :ticket_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ticket_id' => $ticketId]);
        return $stmt->fetch();
    }
}
