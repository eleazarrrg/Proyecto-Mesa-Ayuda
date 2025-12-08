<?php
// public/tickets.php
session_start();

require_once __DIR__ . '/../app/models/Ticket.php';

// Protección simple
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$ticketModel = new Ticket();

// Paginación sencilla
$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina < 1) $pagina = 1;

$porPagina = 10;
$offset = ($pagina - 1) * $porPagina;

$total = $ticketModel->contarTodos();
$tickets = $ticketModel->listarTodos($porPagina, $offset);

$totalPaginas = ceil($total / $porPagina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Tickets</title>
</head>
<body>
    <h1>Tickets</h1>

    <p>
        <a href="dashboard.php">Inicio</a> |
        <a href="ticket_nuevo.php">Nuevo Ticket</a>
    </p>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Colaborador</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Prioridad</th>
            <th>Fecha creación</th>
        </tr>
        <?php foreach ($tickets as $t): ?>
            <tr>
                <td><?= $t['id']; ?></td>
                <td><?= $t['titulo']; ?></td>
                <td><?= $t['primer_nombre'] . ' ' . $t['primer_apellido']; ?></td>
                <td><?= $t['tipo_nombre']; ?></td>
                <td><?= $t['estado']; ?></td>
                <td><?= $t['prioridad']; ?></td>
                <td><?= $t['fecha_creacion']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p>
        Página:
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <?php if ($i == $pagina): ?>
                <strong><?= $i; ?></strong>
            <?php else: ?>
                <a href="?p=<?= $i; ?>"><?= $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </p>
</body>
</html>
