<?php
session_start();

require_once __DIR__ . '/../app/models/Ticket.php';
require_once __DIR__ . '/../app/models/Encuesta.php';
require_once __DIR__ . '/../app/core/Validator.php';

// Opción: podrías permitir encuesta sin login, pero para el examen
// lo dejamos protegido.
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['ticket_id'])) {
    die("Ticket no especificado.");
}

$ticketId = (int)$_GET['ticket_id'];

$ticketModel = new Ticket();
$encuestaModel = new Encuesta();

$ticket = $ticketModel->obtenerPorId($ticketId);
if (!$ticket) {
    die("Ticket no encontrado.");
}

$encuesta = $encuestaModel->obtenerPorTicket($ticketId);

$errores = [];
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nivel = $_POST['nivel'] ?? '';
    $comentario = Validator::sanitizeString($_POST['comentario'] ?? '');

    if (!in_array($nivel, ['CONFORME','INCONFORME','NO_RESPONDIDO'])) {
        $errores[] = "Debe seleccionar una opción de satisfacción.";
    }

    if (empty($errores)) {
        if ($encuestaModel->crearOActualizar($ticketId, $nivel, $comentario)) {
            $mensaje_exito = "Encuesta guardada correctamente.";
            $encuesta = $encuestaModel->obtenerPorTicket($ticketId);
        } else {
            $errores[] = "Error al guardar la encuesta.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Encuesta de satisfacción - Ticket #<?= $ticketId; ?></title>
</head>
<body>
    <h1>Encuesta de satisfacción</h1>

    <p><a href="tickets.php">Volver a tickets</a></p>

    <h3>Ticket #<?= $ticket['id']; ?> - <?= $ticket['titulo']; ?></h3>
    <p>Estado: <?= $ticket['estado']; ?></p>

    <?php if (!empty($errores)): ?>
        <ul style="color:red;">
            <?php foreach ($errores as $e): ?>
                <li><?= $e; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($mensaje_exito): ?>
        <p style="color:green;"><?= $mensaje_exito; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>¿Está conforme con la atención recibida?</label><br>
        <label>
            <input type="radio" name="nivel" value="CONFORME"
                   <?= ($encuesta['nivel'] ?? '') == 'CONFORME' ? 'checked' : ''; ?>>
            Conforme
        </label><br>
        <label>
            <input type="radio" name="nivel" value="INCONFORME"
                   <?= ($encuesta['nivel'] ?? '') == 'INCONFORME' ? 'checked' : ''; ?>>
            Inconforme
        </label><br>
        <label>
            <input type="radio" name="nivel" value="NO_RESPONDIDO"
                   <?= ($encuesta['nivel'] ?? '') == 'NO_RESPONDIDO' ? 'checked' : ''; ?>>
            No fue respondida
        </label>
        <br><br>

        <label>Comentario adicional (opcional):</label><br>
        <textarea name="comentario" rows="4" cols="60"><?= $encuesta['comentario'] ?? ''; ?></textarea>
        <br><br>

        <button type="submit">Guardar encuesta</button>
    </form>
</body>
</html>
