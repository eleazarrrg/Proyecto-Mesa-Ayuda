<?php
session_start();
require_once __DIR__ . '/../app/models/TipoTicket.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$model = new TipoTicket();
$tipos = $model->listarTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tipos de Ticket</title>
</head>
<body>
    <h1>Tipos de Ticket</h1>

    <p>
        <a href="dashboard.php">Inicio</a> |
        <a href="tipo_ticket_form.php">Nuevo tipo</a>
    </p>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($tipos as $t): ?>
            <tr>
                <td><?= $t['id']; ?></td>
                <td><?= $t['nombre']; ?></td>
                <td><?= $t['categoria']; ?></td>
                <td>
                    <a href="tipo_ticket_form.php?id=<?= $t['id']; ?>">Editar</a> |
                    <a href="tipo_ticket_eliminar.php?id=<?= $t['id']; ?>"
                       onclick="return confirm('¿Eliminar tipo de ticket?');">
                       Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
