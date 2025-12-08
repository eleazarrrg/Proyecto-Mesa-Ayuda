<?php
session_start();

require_once __DIR__ . '/../app/models/Ticket.php';
require_once __DIR__ . '/../app/core/Database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$ticketModel = new Ticket();

// Filtros simples
$filtros = [];
if (!empty($_GET['estado'])) {
    $filtros['estado'] = $_GET['estado'];
}
if (!empty($_GET['tipo_ticket_id'])) {
    $filtros['tipo_ticket_id'] = $_GET['tipo_ticket_id'];
}
if (!empty($_GET['agente_id'])) {
    $filtros['agente_id'] = $_GET['agente_id'];
}

$datos = $ticketModel->listarParaReporte($filtros);

// Necesitamos tipos_ticket y agentes para selects
$db = Database::getInstance()->getConnection();
$tipos = $db->query("SELECT id, nombre FROM tipos_ticket ORDER BY nombre")->fetchAll();
$agentes = $db->query("SELECT id, nombre FROM usuarios WHERE rol_id IN (1,2) ORDER BY nombre")->fetchAll();

// Export a Excel si ?export=1
if (isset($_GET['export']) && $_GET['export'] == 1) {
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"reporte_tickets.xls\"");
    echo "<table border='1'>";
    echo "<tr>
            <th>ID</th>
            <th>Título</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Prioridad</th>
            <th>Agente</th>
            <th>Fecha creación</th>
            <th>Fecha respuesta</th>
            <th>Fecha cierre</th>
            <th>Minutos respuesta</th>
          </tr>";
    foreach ($datos as $r) {
        echo "<tr>
                <td>{$r['id']}</td>
                <td>{$r['titulo']}</td>
                <td>{$r['tipo_nombre']}</td>
                <td>{$r['estado']}</td>
                <td>{$r['prioridad']}</td>
                <td>".($r['agente_nombre'] ?? '')."</td>
                <td>{$r['fecha_creacion']}</td>
                <td>{$r['fecha_respuesta']}</td>
                <td>{$r['fecha_cierre']}</td>
                <td>{$r['minutos_respuesta']}</td>
              </tr>";
    }
    echo "</table>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes de Tickets</title>
</head>
<body>
    <h1>Reportes de Tickets</h1>

    <p><a href="dashboard.php">Inicio</a></p>

    <form method="GET">
        <label>Estado:</label>
        <select name="estado">
            <option value="">-- Todos --</option>
            <option value="ABIERTO" <?= (($_GET['estado'] ?? '')=='ABIERTO')?'selected':''; ?>>Abierto</option>
            <option value="EN_PROCESO" <?= (($_GET['estado'] ?? '')=='EN_PROCESO')?'selected':''; ?>>En proceso</option>
            <option value="CERRADO" <?= (($_GET['estado'] ?? '')=='CERRADO')?'selected':''; ?>>Cerrado</option>
        </select>

        <label>Tipo:</label>
        <select name="tipo_ticket_id">
            <option value="">-- Todos --</option>
            <?php foreach ($tipos as $t): ?>
                <option value="<?= $t['id']; ?>" <?= (($_GET['tipo_ticket_id'] ?? '')==$t['id'])?'selected':''; ?>>
                    <?= $t['nombre']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Agente:</label>
        <select name="agente_id">
            <option value="">-- Todos --</option>
            <?php foreach ($agentes as $a): ?>
                <option value="<?= $a['id']; ?>" <?= (($_GET['agente_id'] ?? '')==$a['id'])?'selected':''; ?>>
                    <?= $a['nombre']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Filtrar</button>
        <button type="submit" name="export" value="1">Exportar a Excel</button>
    </form>

    <br>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Prioridad</th>
            <th>Agente</th>
            <th>Fecha creación</th>
            <th>Fecha respuesta</th>
            <th>Fecha cierre</th>
            <th>Minutos respuesta</th>
        </tr>
        <?php foreach ($datos as $r): ?>
            <tr>
                <td><?= $r['id']; ?></td>
                <td><?= $r['titulo']; ?></td>
                <td><?= $r['tipo_nombre']; ?></td>
                <td><?= $r['estado']; ?></td>
                <td><?= $r['prioridad']; ?></td>
                <td><?= $r['agente_nombre']; ?></td>
                <td><?= $r['fecha_creacion']; ?></td>
                <td><?= $r['fecha_respuesta']; ?></td>
                <td><?= $r['fecha_cierre']; ?></td>
                <td><?= $r['minutos_respuesta']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
