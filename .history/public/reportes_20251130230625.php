<?php
session_start();

require_once __DIR__ . '/../app/models/Ticket.php';
require_once __DIR__ . '/../app/core/Database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$ticketModel = new Ticket();
$db          = Database::getInstance()->getConnection();

// -----------------------------
//  Filtros
// -----------------------------
$filtros = [];

// Estado: por defecto solo tickets culminados (atendidos)
$estadoDefault = 'CULMINADA';
if (isset($_GET['estado']) && $_GET['estado'] !== '') {
    $estadoSeleccionado   = $_GET['estado'];
    $filtros['estado']    = $estadoSeleccionado;
} else {
    $estadoSeleccionado   = $estadoDefault;
    $filtros['estado']    = $estadoDefault;
}

// Tipo de ticket
$tipoSeleccionado = $_GET['tipo_ticket_id'] ?? '';
if ($tipoSeleccionado !== '') {
    $filtros['tipo_ticket_id'] = (int)$tipoSeleccionado;
}

// Agente
$agenteSeleccionado = $_GET['agente_id'] ?? '';
if ($agenteSeleccionado !== '') {
    $filtros['agente_id'] = (int)$agenteSeleccionado;
}

// Datos completos (se usan para estadísticas, Excel y para calcular la paginación)
$datos = $ticketModel->listarParaReporte($filtros);

// -----------------------------
//  Estadísticas básicas (punto 9)
// -----------------------------
$totalTickets = count($datos);
$porCategoria = [];
$porAgente    = [];
$sumaMinutos  = 0;
$conMinutos   = 0;

foreach ($datos as $r) {
    // categoría técnico / académico
    $cat = $r['categoria'] ?? 'SIN_CATEGORIA';
    if (!isset($porCategoria[$cat])) {
        $porCategoria[$cat] = 0;
    }
    $porCategoria[$cat]++;

    // agente
    $agente = $r['agente_nombre'] ?? 'Sin agente';
    if (!isset($porAgente[$agente])) {
        $porAgente[$agente] = 0;
    }
    $porAgente[$agente]++;

    // tiempo de respuesta
    if ($r['minutos_respuesta'] !== null) {
        $sumaMinutos += (int)$r['minutos_respuesta'];
        $conMinutos++;
    }
}

$promedioMinutos = $conMinutos > 0 ? round($sumaMinutos / $conMinutos, 2) : 0.0;

// -----------------------------
//  Paginación del reporte (detalle)
// -----------------------------
$porPagina = 10; // cambia este número si quieres más/menos filas por página
$pagina    = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina < 1) {
    $pagina = 1;
}

$totalPaginas = max(1, (int)ceil($totalTickets / $porPagina));
if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

$offset         = ($pagina - 1) * $porPagina;
$datosPaginados = array_slice($datos, $offset, $porPagina);

// -----------------------------
//  Exportar a Excel (usa TODOS los datos, sin paginación)
// -----------------------------
if (isset($_GET['export']) && $_GET['export'] == '1') {
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="reporte_tickets.xls"');

    echo "<h3>Estadísticas de tickets</h3>";
    echo "<p>Total de tickets: {$totalTickets}</p>";
    echo "<p>Promedio de minutos de respuesta: {$promedioMinutos}</p>";

    echo "<h4>Por categoría</h4>";
    echo "<table border='1'>";
    echo "<tr><th>Categoría</th><th>Cantidad</th></tr>";
    foreach ($porCategoria as $cat => $cnt) {
        echo "<tr><td>{$cat}</td><td>{$cnt}</td></tr>";
    }
    echo "</table><br>";

    echo "<h4>Por agente</h4>";
    echo "<table border='1'>";
    echo "<tr><th>Agente</th><th>Cantidad de tickets</th></tr>";
    foreach ($porAgente as $agente => $cnt) {
        echo "<tr><td>{$agente}</td><td>{$cnt}</td></tr>";
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

// -----------------------------
//  Datos auxiliares para los filtros (catálogos)
// -----------------------------
$tipos   = $db->query('SELECT id, nombre FROM tipos_ticket ORDER BY nombre')->fetchAll();
$agentes = $db->query('SELECT id, nombre FROM usuarios WHERE activo = 1 ORDER BY nombre')->fetchAll();
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
            <option value="EN_ESPERA"  <?= ($estadoSeleccionado === 'EN_ESPERA')  ? 'selected' : ''; ?>>En espera</option>
            <option value="EN_PROCESO" <?= ($estadoSeleccionado === 'EN_PROCESO') ? 'selected' : ''; ?>>En proceso</option>
            <option value="CULMINADA"  <?= ($estadoSeleccionado === 'CULMINADA')  ? 'selected' : ''; ?>>Culminada</option>
        </select>

        <label>Tipo:</label>
        <select name="tipo_ticket_id">
            <option value="">-- Todos --</option>
            <?php foreach ($tipos as $t): ?>
                <option value="<?= $t['id']; ?>" <?= ($tipoSeleccionado == $t['id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($t['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Agente:</label>
        <select name="agente_id">
            <option value="">-- Todos --</option>
            <?php foreach ($agentes as $a): ?>
                <option value="<?= $a['id']; ?>" <?= ($agenteSeleccionado == $a['id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($a['nombre']); ?>
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
            <th>Cantidad</th>
        </tr>
        <?php foreach ($porAgente as $agente => $cnt): ?>
            <tr>
                <td><?= htmlspecialchars($agente); ?></td>
                <td><?= $cnt; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br>

    <h3>Detalle de tickets (paginado)</h3>
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
            <th>Acciones</th>
        </tr>
        <?php if (empty($datosPaginados)): ?>
            <tr>
                <td colspan="9">No hay tickets para los filtros seleccionados.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($datosPaginados as $r): ?>
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
        <?php endif; ?>
    </table>

    <?php if ($totalPaginas > 1): ?>
        <p>
            Página:
            <?php
            // Construir enlaces de paginación preservando los filtros
            $queryBase = $_GET;
            for ($i = 1; $i <= $totalPaginas; $i++):
                $queryBase['p'] = $i;
                $url = '?' . htmlspecialchars(http_build_query($queryBase));
                if ($i === $pagina):
            ?>
                    <strong><?= $i; ?></strong>
                <?php else: ?>
                    <a href="<?= $url; ?>"><?= $i; ?></a>
                <?php
                endif;
            endfor;
            ?>
        </p>
    <?php endif; ?>
</body>
</html>
