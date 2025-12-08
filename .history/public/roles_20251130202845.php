<?php
// public/roles.php
session_start();

require_once __DIR__ . '/../app/models/Rol.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Solo admin (rol 1) puede ver roles
if (($_SESSION['role_id'] ?? null) != 1) {
    die("Acceso denegado. Solo administradores.");
}

$model = new Rol();
$roles = $model->listarTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Roles del sistema</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Roles del sistema</h1>

    <p>
        <a href="dashboard.php">Inicio</a> |
        <a href="rol_form.php">Nuevo rol</a>
    </p>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Alcance / Permisos</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($roles as $r): ?>
            <tr>
                <td><?= $r['id']; ?></td>
                <td><?= htmlspecialchars($r['nombre']); ?></td>
                <td><?= htmlspecialchars($r['alcance'] ?? ''); ?></td>
                <td>
                    <a href="rol_form.php?id=<?= $r['id']; ?>">Editar</a>
                    <?php if ($r['id'] > 1): ?>
                        | <a href="rol_eliminar.php?id=<?= $r['id']; ?>"
                             onclick="return confirm('¿Eliminar este rol?');">
                            Eliminar
                          </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
