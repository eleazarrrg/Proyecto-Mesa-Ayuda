<?php
// public/tickets.php
session_start();

require_once __DIR__ . '/../app/models/Ticket.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$ticketModel  = new Ticket();

$roleId       = $_SESSION['role_id'] ?? null;
$userId       = $_SESSION['user_id'] ?? null;
$esEstudiante = ($roleId == 3);

// Filtro de estado (opcional)
$estadoFiltro = $_GET['estado'] ?? '';
$estadoFiltro = in_array($estadoFiltro, ['EN_PROCESO', 'EN_ESPERA', 'CULMINADA'], true)
    ? $estadoFiltro
    : '';

// Paginación
$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina < 1) {
    $pagina = 1;
}
$porPagina = 10;
$offset    = ($pagina - 1) * $porPagina;

// Si es estudiante, solo ve sus tickets
if ($esEstudiante) {
    $total   = $ticketModel->contarPorCreador($userId, $estadoFiltro ?: null);
    $tickets = $ticketModel->listarPorCreador($userId, $porPagina, $offset, $estadoFiltro ?: null);
} else {
    // Admin / Agente ven todos
    $total   = $ticketModel->contarTodos($estadoFiltro ?: null);
    $tickets = $ticketModel->listarTodos($porPagina, $offset, $estadoFiltro ?: null);
}

$totalPaginas = max(1, (int)ceil($total / $porPagina));

// Para mantener el filtro en la paginación
$queryEstado = $estadoFiltro ? '&estado=' . urlencode($estadoFiltro) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Tickets</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Tickets</h1>

    <p>
        <a href="dashboard.php">Inicio</a>
        <?php if ($esEstudiante): ?>
            | <a href="ticket_nuevo.php">Nuevo Ticket</a>
        <?php endif; ?>
    </p>

    <!-- Filtro por estado -->
    <form method="get" style="margin-bottom: 10px; max-width: 400px;">
        <label for="estado">Filtrar por estado:</label>
        <select name="estado" id="estado" onchange="this.form.submit()">
            <option value="">-- Todos --</option>
            <option value="EN_ESPERA"   <?= $estadoFiltro === 'EN_ESPERA'   ? 'selected' : ''; ?>>En espera</option>
            <option value="EN_PROCESO"  <?= $estadoFiltro === 'EN_PROCESO'  ? 'selected' : ''; ?>>En proceso</option>
            <option value="CULMINADA"   <?= $estadoFiltro === 'CULMINADA'   ? 'selected' : ''; ?>>Culminada</option>
        </select>
        <!-- Si quieres un botón en vez de onchange:
        <button type="submit">Aplicar</button>
        -->
    </form>

    <table>
        <thead>
        <tr>
            <?php if (!$esEstudiante): ?>
                <th>ID</th>
            <?php endif; ?>
            <th>Título</th>
            <th>Estudiante</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Prioridad</th>
            <th>Fecha creación</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($tickets)): ?>
            <tr>
                <td colspan="<?= $esEstudiante ? 7 : 8; ?>">No hay tickets registrados.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($tickets as $t): ?>
                <tr>
                    <?php if (!$esEstudiante): ?>
                        <td data-label="ID"><?= $t['id']; ?></td>
                    <?php endif; ?>
                    <td data-label="Título"><?= htmlspecialchars($t['titulo']); ?></td>
                    <td data-label="Estudiante"><?= htmlspecialchars($t['primer_nombre'] . ' ' . $t['primer_apellido']); ?></td>
                    <td data-label="Tipo"><?= htmlspecialchars($t['tipo_nombre']); ?></td>
                    <td data-label="Estado"><?= htmlspecialchars($t['estado']); ?></td>
                    <td data-label="Prioridad"><?= htmlspecialchars($t['prioridad']); ?></td>
                    <td data-label="Fecha creación"><?= $t['fecha_creacion']; ?></td>
                    <td data-label="Acciones">
                        <a href="ticket_detalle.php?id=<?= $t['id']; ?>">Ver / Gestionar</a>
                        <?php if ($esEstudiante && $t['estado'] === 'CULMINADA'): ?>
                            | <a href="encuesta.php?ticket_id=<?= $t['id']; ?>">Encuesta</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <p>
        Página:
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <?php if ($i == $pagina): ?>
                <strong><?= $i; ?></strong>
            <?php else: ?>
                <a href="?p=<?= $i . $queryEstado; ?>"><?= $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </p>
</body>
</html>
