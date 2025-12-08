<?php
// public/ticket_nuevo.php
session_start();

require_once __DIR__ . '/../app/core/Validator.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/models/Ticket.php';

// Simple protección, si no hay login, redirigir
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Obtener colaboradores para el select
$colabStmt = $db->query("SELECT id, primer_nombre, primer_apellido FROM colaboradores ORDER BY primer_nombre");
$colaboradores = $colabStmt->fetchAll();

// Obtener tipos de ticket
$tipoStmt = $db->query("SELECT id, nombre FROM tipos_ticket ORDER BY nombre");
$tipos = $tipoStmt->fetchAll();

$errores = [];
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $colaborador_id = $_POST['colaborador_id'] ?? '';
    $tipo_ticket_id = $_POST['tipo_ticket_id'] ?? '';
    $titulo         = Validator::sanitizeString($_POST['titulo'] ?? '');
    $descripcion    = Validator::sanitizeString($_POST['descripcion'] ?? '');
    $prioridad      = $_POST['prioridad'] ?? 'MEDIA';

    if (!Validator::required($colaborador_id)) {
        $errores[] = "Debe seleccionar un colaborador.";
    }
    if (!Validator::required($tipo_ticket_id)) {
        $errores[] = "Debe seleccionar un tipo de ticket.";
    }
    if (!Validator::required($titulo)) {
        $errores[] = "El título es obligatorio.";
    }
    if (!Validator::required($descripcion)) {
        $errores[] = "La descripción es obligatoria.";
    }

    if (empty($errores)) {
        $ticketModel = new Ticket();

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $data = [
            'colaborador_id'        => $colaborador_id,
            'tipo_ticket_id'        => $tipo_ticket_id,
            'titulo'                => $titulo,
            'descripcion'           => $descripcion,
            'prioridad'             => $prioridad,
            'creado_por_usuario_id' => $_SESSION['user_id'],
            'ip_origen'             => $ip
        ];

        if ($ticketModel->crear($data)) {
            $mensaje_exito = "Ticket creado correctamente.";
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
</head>
<body>
    <h1>Crear nuevo Ticket</h1>

    <p><a href="tickets.php">Volver al listado</a></p>

    <?php if (!empty($errores)): ?>
        <ul style="color:red;">
            <?php foreach ($errores as $e): ?>
                <li><?= $e ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($mensaje_exito): ?>
        <p style="color:green;"><?= $mensaje_exito ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Colaborador / Estudiante:</label><br>
        <select name="colaborador_id">
            <option value="">--Seleccione--</option>
            <?php foreach ($colaboradores as $c): ?>
                <option value="<?= $c['id']; ?>">
                    <?= $c['primer_nombre'] . ' ' . $c['primer_apellido']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Tipo de Ticket:</label><br>
        <select name="tipo_ticket_id">
            <option value="">--Seleccione--</option>
            <?php foreach ($tipos as $t): ?>
                <option value="<?= $t['id']; ?>">
                    <?= $t['nombre']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Título:</label><br>
        <input type="text" name="titulo" size="60">
        <br><br>

        <label>Descripción:</label><br>
        <textarea name="descripcion" rows="5" cols="60"></textarea>
        <br><br>

        <label>Prioridad:</label><br>
        <select name="prioridad">
            <option value="BAJA">Baja</option>
            <option value="MEDIA" selected>Media</option>
            <option value="ALTA">Alta</option>
        </select>
        <br><br>

        <button type="submit">Guardar Ticket</button>
    </form>
</body>
</html>
