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

// Por defecto, mostrar solo tickets culminados (como pide el punto 8)
$estadoDefault = 'CULMINADA';
if (isset($_GET['estado']) && $_GET['estado'] !== '') {
    $filtros['estado'] = $_GET['estado'];
} else {
    $filtros['estado'] = $estadoDefault;
    $_GET['estado'] = $estadoDefault;
}

if (!empty($_GET['tipo_ticket_id'])) {
    $filtros['tipo_ticket_id'] = $_GET['tipo_ticket_id'];
}
if (!empty($_GET['agente_id'])) {
    $filtros['agente_id'] = $_GET['agente_id'];
}

$datos = $ticketModel->listarParaReporte($filtros);

// Tipos y agentes para filtros
$db = Database::getInstance()->getConnection();
$tipos = $db->query("SELECT id, nombre FROM tipos_ticket ORDER BY nombre")->fetchAll();
$agentes = $db->query("SELECT id, nombre FROM usuarios WHERE activo = 1 ORDER BY nombre")->fetchAll();

// Estadísticas básicas (punto 9)
$totalTickets   = count($datos);
$porCategoria   = [];
$porAgente      = [];
$sumaMinutos    = 0;
$conMinutos     = 0;

foreach ($datos as $r) {
    // categoría técnico / académico
    $cat = $r['categoria'] ?? 'SIN_CATEGORIA';
    if (!isset($porCategoria[$cat])) $porCategoria[$cat] = 0;
    $porCategoria[$cat]++;

    // agente
    $ag = $r['agente_nombre'] ?? 'Sin agente';
    if (!isset($porAgente[$ag])) $porAgente[$ag] = 0;
    $porAgente[$ag]++;

    // tiempo de respuesta
    if ($r['minutos_respuesta'] !== null) {
        $sumaMinutos += (int)$r['minutos_respuesta'];
        $conMinutos++;
    }
}
$promedioMinutos = $conMinutos > 0 ? round($sumaMinutos / $conMinutos, 2) : 0;

// Exportar a Excel
if (isset($_GET['export']) && $_GET['export'] == 1) {
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"reporte_tickets.xls\"");

    echo "<h3>Estadísticas de tickets</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Total de tickets</th><td>{$totalTickets}</td></tr>";
    echo "<tr><th>Promedio minutos de respuesta</th><td>{$promedioMinutos}</td></tr>";
    echo "</table><br>";

    echo "<table border='1'>";
    echo "<tr><th>Categoría</th><th>Cantidad</th></tr>";
    foreach ($porCategoria as $cat => $cnt) {
        echo "<tr><td>{$cat}</td><td>{$cnt}</td></tr>";
    }
    echo "</table><br>";

    echo "<table border='1'>";
    echo "<tr><th>Agente</th><th>Cantidad de tickets</th></tr>";
    foreach ($porAgente as $ag => $cnt) {
        echo "<tr><td>{$ag}</td><td>{$cnt}</td></tr>";
    }
    echo "</table><br>";

    echo "<h3>Detalle de tickets</h3>";
    echo "<table border='1'>";
    echo "<tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descripción</th>
            <th>Solución</th>
            <th>Categoría</th>
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
                <td>{$r['descripcion']}</td>
                <td>{$r['solucion']}</td>
                <td>{$r['categoria']}</td>
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
    <title>Reportes y estadísticas de Tickets</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Reportes y estadísticas de Tickets</h1>

    <p><a href="dashboard.php">Inicio</a></p>

    <h3>Filtros</h3>
    <form method="GET">
        <label>Estado:</label>
        <select name="estado">
            <option value="">-- Todos --</option>
            <option value="EN_ESPERA"   <?= (($_GET['estado'] ?? '')=='EN_ESPERA')   ? 'selected' : ''; ?>>En espera</option>
            <option value="EN_PROCESO"  <?= (($_GET['estado'] ?? '')=='EN_PROCESO')  ? 'selected' : ''; ?>>En proceso</option>
            <option value="CULMINADA"   <?= (($_GET['estado'] ?? '')=='CULMINADA')   ? 'selected' : ''; ?>>Culminada</option>
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

    <h3>Estadísticas (según filtros aplicados)</h3>
    <p><strong>Total de tickets:</strong> <?= $totalTickets; ?></p>
    <p><strong>Promedio de minutos de respuesta:</strong> <?= $promedioMinutos; ?></p>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Categoría</th>
            <th>Cantidad</th>
        </tr>
        <?php foreach ($porCategoria as $cat => $cnt): ?>
            <tr>
                <td><?= htmlspecialchars($cat); ?></td>
                <td><?= $cnt; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Agente</th>
            <th>Cantidad de tickets</th>
        </tr>
        <?php foreach ($porAgente as $ag => $cnt): ?>
            <tr>
                <td><?= htmlspecialchars($ag); ?></td>
                <td><?= $cnt; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Detalle de Tickets</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Categoría</th>
            <th>Estado</th>
            <th>Prioridad</th>
            <th>Agente</th>
            <th>Fecha creación</th>
            <th>Minutos respuesta</th>
            <th>Ver detalle</th>
        </tr>
        <?php foreach ($datos as $r): ?>
            <tr>
                <td><?= $r['id']; ?></td>
                <td><?= htmlspecialchars($r['titulo']); ?></td>
                <td><?= htmlspecialchars($r['categoria']); ?></td>
                <td><?= $r['estado']; ?></td>
                <td><?= $r['prioridad']; ?></td>
                <td><?= htmlspecialchars($r['agente_nombre'] ?? ''); ?></td>
                <td><?= $r['fecha_creacion']; ?></td>
                <td><?= $r['minutos_respuesta']; ?></td>
                <td><a href="ticket_detalle.php?id=<?= $r['id']; ?>">Ver detalle</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
