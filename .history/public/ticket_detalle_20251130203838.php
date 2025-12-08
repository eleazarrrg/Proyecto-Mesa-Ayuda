<?php
// public/ticket_detalle.php
session_start();

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Validator.php';
require_once __DIR__ . '/../app/models/Ticket.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id'])) {
    die("Ticket no especificado.");
}

$id = (int)$_GET['id'];

$ticketModel = new Ticket();
$db = Database::getInstance()->getConnection();

$ticket = $ticketModel->obtenerPorId($id);
if (!$ticket) {
    die("Ticket no encontrado.");
}

// Obtener agentes (usuarios con rol admin o agente)
$agentes = $db->query("SELECT id, nombre FROM usuarios WHERE rol_id IN (1,2) AND activo = 1 ORDER BY nombre")
              ->fetchAll();

$errores = [];
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estado    = $_POST['estado'] ?? $ticket['estado'];
    $prioridad = $_POST['prioridad'] ?? $ticket['prioridad'];
    $solucion  = Validator::sanitizeString($_POST['solucion'] ?? '');
    $agenteId  = $_POST['agente_id'] !== '' ? (int)$_POST['agente_id'] : null;

    if (!in_array($estado, ['ABIERTO','EN_PROCESO','CERRADO'])) {
        $errores[] = "Estado inválido.";
    }
    if (!in_array($prioridad, ['BAJA','MEDIA','ALTA'])) {
        $errores[] = "Prioridad inválida.";
    }

    if (empty($errores)) {
        if ($ticketModel->actualizarGestion($id, $estado, $prioridad, $solucion, $agenteId)) {
            $mensaje_exito = "Ticket actualizado correctamente.";
            // recargar datos
            $ticket = $ticketModel->obtenerPorId($id);
        } else {
            $errores[] = "Error al actualizar el ticket.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #<?= $ticket['id']; ?> - Detalle</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Detalle de Ticket #<?= $ticket['id']; ?></h1>

    <p>
        <a href="tickets.php">Volver al listado</a>
    </p>

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

    <h3>Información general</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Título</th>
            <td><?= htmlspecialchars($ticket['titulo']); ?></td>
        </tr>
        <tr>
            <th>Colaborador</th>
            <td><?= htmlspecialchars($ticket['primer_nombre'] . ' ' . $ticket['primer_apellido']); ?></td>
        </tr>
        <tr>
            <th>Identificación</th>
            <td><?= htmlspecialchars($ticket['identificacion']); ?></td>
        </tr>
        <tr>
            <th>Tipo de Ticket</th>
            <td><?= htmlspecialchars($ticket['tipo_nombre']); ?></td>
        </tr>
        <tr>
            <th>IP de origen</th>
            <td><?= htmlspecialchars($ticket['ip_origen']); ?></td>
        </tr>
        <tr>
            <th>Fecha creación</th>
            <td><?= $ticket['fecha_creacion']; ?></td>
        </tr>
        <tr>
            <th>Fecha asignación</th>
            <td><?= $ticket['fecha_asignacion'] ?? '-'; ?></td>
        </tr>
        <tr>
            <th>Fecha respuesta</th>
            <td><?= $ticket['fecha_respuesta'] ?? '-'; ?></td>
        </tr>
        <tr>
            <th>Fecha cierre</th>
            <td><?= $ticket['fecha_cierre'] ?? '-'; ?></td>
        </tr>
    </table>

    <h3>Gestión del Ticket</h3>

    <form method="POST">
        <label>Estado:</label><br>
        <select name="estado">
            <option value="EN_ESPERA"   <?= $ticket['estado'] == 'EN_ESPERA'   ? 'selected' : ''; ?>>En espera</option>
            <option value="EN_PROCESO"  <?= $ticket['estado'] == 'EN_PROCESO'  ? 'selected' : ''; ?>>En proceso</option>
            <option value="CULMINADA"   <?= $ticket['estado'] == 'CULMINADA'   ? 'selected' : ''; ?>>Culminada</option>
        </select>
        <br><br>

        <label>Prioridad:</label><br>
        <select name="prioridad">
            <option value="BAJA"  <?= $ticket['prioridad'] == 'BAJA'  ? 'selected' : ''; ?>>Baja</option>
            <option value="MEDIA" <?= $ticket['prioridad'] == 'MEDIA' ? 'selected' : ''; ?>>Media</option>
            <option value="ALTA"  <?= $ticket['prioridad'] == 'ALTA'  ? 'selected' : ''; ?>>Alta</option>
        </select>
        <br><br>

        <label>Agente asignado:</label><br>
        <select name="agente_id">
            <option value="">-- Sin asignar --</option>
            <?php foreach ($agentes as $a): ?>
                <option value="<?= $a['id']; ?>"
                    <?= ($ticket['agente_id'] ?? null) == $a['id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($a['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Descripción (solo lectura):</label><br>
        <textarea rows="4" cols="70" disabled><?= htmlspecialchars($ticket['descripcion']); ?></textarea>
        <br><br>

        <label>Solución / Comentario del agente:</label><br>
        <textarea name="solucion" rows="5" cols="70"><?= htmlspecialchars($ticket['solucion'] ?? ''); ?></textarea>
        <br><br>

        <button type="submit">Guardar cambios</button>
    </form>
</body>
</html>
