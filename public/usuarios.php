<?php
// public/usuarios.php
session_start();

require_once __DIR__ . '/../app/models/Usuario.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// solo admin
if (($_SESSION['role_id'] ?? null) != 1) {
    die("Acceso denegado. Solo administradores.");
}

$model = new Usuario();

$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina < 1) $pagina = 1;

$porPagina = 10;
$offset = ($pagina - 1) * $porPagina;

$total = $model->contarTodos();
$usuarios = $model->listarTodos($porPagina, $offset);
$totalPaginas = max(1, ceil($total / $porPagina));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios del sistema</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Usuarios del sistema</h1>

    <p>
        <a href="dashboard.php">Inicio</a> |
        <a href="usuario_form.php">Nuevo usuario</a>
    </p>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Activo</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= $u['id']; ?></td>
                <td><?= htmlspecialchars($u['nombre']); ?></td>
                <td><?= htmlspecialchars($u['username']); ?></td>
                <td><?= htmlspecialchars($u['email']); ?></td>
                <td><?= htmlspecialchars($u['rol_nombre'] ?? ''); ?></td>
                <td><?= $u['activo'] ? 'Sí' : 'No'; ?></td>
                <td>
                    <a href="usuario_form.php?id=<?= $u['id']; ?>">Editar</a>
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        | <a href="usuario_eliminar.php?id=<?= $u['id']; ?>"
                             onclick="return confirm('¿Inactivar este usuario?');">
                            Inactivar
                          </a>
                    <?php endif; ?>
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
