<?php
// public/ticket_nuevo.php
session_start();

require_once __DIR__ . '/../app/core/Validator.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/models/Ticket.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Para selects
$colaboradores = $db->query("SELECT id, primer_nombre, primer_apellido FROM colaboradores ORDER BY primer_nombre")->fetchAll();
$tipos = $db->query("SELECT id, nombre FROM tipos_ticket ORDER BY nombre")->fetchAll();

$errores = [];
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo        = Validator::sanitizeString($_POST['titulo'] ?? '');
    $descripcion   = Validator::sanitizeString($_POST['descripcion'] ?? '');
    $colaboradorId = (int)($_POST['colaborador_id'] ?? 0);
    $tipoTicketId  = (int)($_POST['tipo_ticket_id'] ?? 0);
    $prioridad     = $_POST['prioridad'] ?? 'MEDIA';

    if (!Validator::required($titulo)) {
        $errores[] = "El título es obligatorio.";
    }
    if ($colaboradorId <= 0) {
        $errores[] = "Debe seleccionar un colaborador.";
    }
    if ($tipoTicketId <= 0) {
        $errores[] = "Debe seleccionar un tipo de ticket.";
    }

    if (empty($errores)) {
        $ticketModel = new Ticket();
        $data = [
            'titulo'         => $titulo,
            'descripcion'    => $descripcion,
            'colaborador_id' => $colaboradorId,
            'tipo_ticket_id' => $tipoTicketId,
            'prioridad'      => $prioridad,
            'creado_por'     => $_SESSION['user_id'],
            'ip_creacion'    => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        ];

        if ($ticketModel->crear($data)) {
            $mensaje_exito = "Ticket creado correctamente.";
            $titulo = $descripcion = '';
            $colaboradorId = $tipoTicketId = 0;
            $prioridad = 'MEDIA';
        } else {
            $errores[] = "Error al crear el ticket.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Ticket</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Nuevo Ticket</h1>

    <p><a href="tickets.php">Volver al listado</a></p>

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
        <label>Título:</label><br>
        <input type="text" name="titulo" value="<?= htmlspecialchars($titulo ?? ''); ?>"><br><br>

        <label>Descripción:</label><br>
        <textarea name="descripcion" rows="4"><?= htmlspecialchars($descripcion ?? ''); ?></textarea><br><br>

        <label>Colaborador / Estudiante:</label><br>
        <select name="colaborador_id">
            <option value="0">-- Seleccione --</option>
            <?php foreach ($colaboradores as $c): ?>
                <option value="<?= $c['id']; ?>" <?= (isset($colaboradorId) && $colaboradorId == $c['id']) ? 'selected' : ''; ?>>
                    <?= $c['primer_nombre'] . ' ' . $c['primer_apellido']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Tipo de Ticket:</label><br>
        <select name="tipo_ticket_id">
            <option value="0">-- Seleccione --</option>
            <?php foreach ($tipos as $t): ?>
                <option value="<?= $t['id']; ?>" <?= (isset($tipoTicketId) && $tipoTicketId == $t['id']) ? 'selected' : ''; ?>>
                    <?= $t['nombre']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Prioridad:</label><br>
        <select name="prioridad">
            <option value="BAJA"  <?= (isset($prioridad) && $prioridad == 'BAJA')  ? 'selected' : ''; ?>>Baja</option>
            <option value="MEDIA" <?= (!isset($prioridad) || $prioridad == 'MEDIA') ? 'selected' : ''; ?>>Media</option>
            <option value="ALTA"  <?= (isset($prioridad) && $prioridad == 'ALTA')  ? 'selected' : ''; ?>>Alta</option>
        </select>
        <br><br>

        <button type="submit">Guardar Ticket</button>
    </form>
</body>
</html>
