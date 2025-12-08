<?php
session_start();

require_once __DIR__ . '/../app/models/Ticket.php';
require_once __DIR__ . '/../app/models/Encuesta.php';
require_once __DIR__ . '/../app/core/Validator.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$roleId       = $_SESSION['role_id'] ?? null;
$userId       = $_SESSION['user_id'] ?? null;
$esEstudiante = ($roleId == 3);

// Solo estudiantes responden encuesta
if (!$esEstudiante) {
    header('Location: tickets.php');
    exit;
}

if (!isset($_GET['ticket_id'])) {
    header('Location: tickets.php');
    exit;
}

$ticketId = (int)$_GET['ticket_id'];

$ticketModel   = new Ticket();
$encuestaModel = new Encuesta();

$ticket = $ticketModel->obtenerPorId($ticketId);
if (!$ticket) {
    header('Location: tickets.php');
    exit;
}

// Seguridad: el ticket debe ser del usuario logeado
if ((int)$ticket['creado_por_usuario_id'] !== (int)$userId) {
    header('Location: tickets.php');
    exit;
}

// Solo encuestas para tickets culminados
if ($ticket['estado'] !== 'CULMINADA') {
    header('Location: tickets.php');
    exit;
}

// ¿Ya tiene encuesta? -> no dejar volver a responder
$encuestaExistente = $encuestaModel->obtenerPorTicket($ticketId);
if ($encuestaExistente) {
    header('Location: tickets.php');
    exit;
}

$errores = [];
$nivel = '';
$comentario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nivel      = $_POST['nivel'] ?? '';
    $comentario = Validator::sanitizeString($_POST['comentario'] ?? '');

    if (!in_array($nivel, ['CONFORME','INCONFORME','NO_RESPONDIDO'], true)) {
        $errores[] = "Debe seleccionar una opción de satisfacción.";
    }

    if (empty($errores)) {
        if ($encuestaModel->crearOActualizar($ticketId, $nivel, $comentario)) {
            // ✅ Al guardar bien, regresamos al listado de tickets
            header('Location: tickets.php');
            exit;
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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Encuesta de satisfacción</h1>

    <p><a href="tickets.php">Volver a tickets</a></p>

    <h3>Ticket #<?= $ticket['id']; ?> - <?= htmlspecialchars($ticket['titulo']); ?></h3>
    <p>Estado: <?= htmlspecialchars($ticket['estado']); ?></p>

    <?php if (!empty($errores)): ?>
        <ul style="color:red;">
            <?php foreach ($errores as $e): ?>
                <li><?= htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">
        <label>¿Está conforme con la atención recibida?</label><br>
        <label>
            <input type="radio" name="nivel" value="CONFORME"
                   <?= $nivel === 'CONFORME' ? 'checked' : ''; ?>>
            Conforme
        </label><br>
        <label>
            <input type="radio" name="nivel" value="INCONFORME"
                   <?= $nivel === 'INCONFORME' ? 'checked' : ''; ?>>
            Inconforme
        </label><br>
        <label>
            <input type="radio" name="nivel" value="NO_RESPONDIDO"
                   <?= $nivel === 'NO_RESPONDIDO' ? 'checked' : ''; ?>>
            No fue respondida
        </label>
        <br><br>

        <label>Comentario adicional (opcional):</label><br>
        <textarea name="comentario" rows="4" cols="60"><?= htmlspecialchars($comentario ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        <br><br>

        <button type="submit">Guardar encuesta</button>
    </form>
</body>
</html>
