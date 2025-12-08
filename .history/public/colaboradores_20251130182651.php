<?php
session_start();
require_once __DIR__ . '/../app/models/Colaborador.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$model = new Colaborador();

$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina < 1) $pagina = 1;

$porPagina = 10;
$offset = ($pagina - 1) * $porPagina;

$total = $model->contarTodos();
$colaboradores = $model->listarTodos($porPagina, $offset);
$totalPaginas = ceil($total / $porPagina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Colaboradores / Estudiantes</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Colaboradores / Estudiantes</h1>

    <p>
        <a href="dashboard.php">Inicio</a> |
        <a href="colaborador_form.php">Nuevo colaborador</a>
    </p>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nombre completo</th>
            <th>Identificación</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Tipo</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($colaboradores as $c): ?>
            <tr>
                <td><?= $c['id']; ?></td>
                <td>
                    <?= $c['primer_nombre'] . ' ' .
                       ($c['segundo_nombre'] ? $c['segundo_nombre'].' ' : '') .
                       $c['primer_apellido'] . ' ' .
                       ($c['segundo_apellido'] ?? ''); ?>
                </td>
                <td><?= $c['identificacion']; ?></td>
                <td><?= $c['email']; ?></td>
                <td><?= $c['telefono']; ?></td>
                <td><?= $c['tipo']; ?></td>
                <td>
                    <a href="colaborador_form.php?id=<?= $c['id']; ?>">Editar</a> |
                    <a href="colaborador_eliminar.php?id=<?= $c['id']; ?>"
                       onclick="return confirm('¿Eliminar colaborador?');">
                       Eliminar
                    </a>
                </td>
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
