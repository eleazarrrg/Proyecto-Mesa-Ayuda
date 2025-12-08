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

$roleId = $_SESSION['role_id'] ?? null;
$userId = $_SESSION['user_id'] ?? null;

// Solo estudiantes pueden crear tickets
if ($roleId != 3) {
    die("No tienes permiso para crear tickets.");
}

$db = Database::getInstance()->getConnection();

// Obtener colaborador correspondiente al usuario (por email)
$stmt = $db->prepare("
    SELECT c.id AS colaborador_id
    FROM colaboradores c
    JOIN usuarios u ON c.email = u.email
    WHERE u.id = :uid
    LIMIT 1
");
$stmt->execute([':uid' => $userId]);
$row = $stmt->fetch();

if (!$row) {
    die("No se encontró el perfil de estudiante asociado a este usuario.");
}

$colaboradorId = (int)$row['colaborador_id'];

// Lista de tipos de ticket
$tipos = $db->query("SELECT id, nombre, categoria FROM tipos_ticket ORDER BY categoria, nombre")->fetchAll();

$errores = [];
$mensaje_exito = '';
$titulo = '';
$descripcion = '';
$tipoTicketId = 0;
$prioridad = 'MEDIA';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo        = Validator::sanitizeString($_POST['titulo'] ?? '');
    $descripcion   = Validator::sanitizeString($_POST['descripcion'] ?? '');
    $tipoTicketId  = (int)($_POST['tipo_ticket_id'] ?? 0);
    $prioridad     = $_POST['prioridad'] ?? 'MEDIA';

    if (!Validator::required($titulo)) {
        $errores[] = "El título es obligatorio.";
    }
    if (!Validator::required($descripcion)) {
        $errores[] = "La descripción es obligatoria.";
    }
    if ($tipoTicketId <= 0) {
        $errores[] = "Debe seleccionar un tipo de ticket.";
    }
    if (!in_array($prioridad, ['BAJA','MEDIA','ALTA'])) {
        $errores[] = "La prioridad seleccionada no es válida.";
    }

    if (empty($errores)) {
        $ticketModel = new Ticket();
        $data = [
            'colaborador_id'        => $colaboradorId,
            'tipo_ticket_id'        => $tipoTicketId,
            'titulo'                => $titulo,
            'descripcion'           => $descripcion,
            'prioridad'             => $prioridad,
            'creado_por_usuario_id' => $userId,
            'ip_origen'             => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        ];

        if ($ticketModel->crear($data)) {
            $mensaje_exito = "Ticket creado correctamente.";
            // Limpiar campos del formulario
            $titulo = '';
            $descripcion = '';
            $tipoTicketId = 0;
            $prioridad = 'MEDIA';
        } else {
            $errores[] = "Error al guardar el ticket.";
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
                <li><?= htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($mensaje_exito): ?>
        <p style="color:green;"><?= htmlspecialchars($mensaje_exito); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Título:</label><br>
        <input type="text" name="titulo" value="<?= htmlspecialchars($titulo); ?>"><br><br>

        <label>Descripción:</label><br>
        <textarea name="descripcion" rows="4" cols="70"><?= htmlspecialchars($descripcion); ?></textarea>
        <br><br>

        <label>Tipo de Ticket:</label><br>
        <select name="tipo_ticket_id">
            <option value="0">-- Seleccione --</option>
            <?php foreach ($tipos as $t): ?>
                <option value="<?= $t['id']; ?>"
                    <?= ($tipoTicketId == $t['id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($t['categoria'] . ' - ' . $t['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Prioridad:</label><br>
        <select name="prioridad">
            <option value="BAJA"  <?= ($prioridad == 'BAJA')  ? 'selected' : ''; ?>>Baja</option>
            <option value="MEDIA" <?= ($prioridad == 'MEDIA') ? 'selected' : ''; ?>>Media</option>
            <option value="ALTA"  <?= ($prioridad == 'ALTA')  ? 'selected' : ''; ?>>Alta</option>
        </select>
        <br><br>

        <button type="submit">Guardar Ticket</button>
    </form>
</body>
</html>
