<?php
// public/rol_form.php
session_start();

require_once __DIR__ . '/../app/core/Validator.php';
require_once __DIR__ . '/../app/models/Rol.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
if (($_SESSION['role_id'] ?? null) != 1) {
    die("Acceso denegado. Solo administradores.");
}

$model = new Rol();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editando = $id > 0;

$errores = [];
$mensaje_exito = '';
$rol = [
    'nombre'  => '',
    'alcance' => 'Acceso limitado a algunos módulos.',
];

if ($editando) {
    $rolBD = $model->obtenerPorId($id);
    if ($rolBD) {
        $rol = $rolBD;
    } else {
        die("Rol no encontrado.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rol['nombre']  = Validator::sanitizeString($_POST['nombre']  ?? '');
    $rol['alcance'] = Validator::sanitizeString($_POST['alcance'] ?? '');

    if (!Validator::required($rol['nombre'])) {
        $errores[] = "El nombre del rol es obligatorio.";
    }

    if (empty($errores)) {
        if ($editando) {
            if ($model->actualizar($id, $rol)) {
                $mensaje_exito = "Rol actualizado correctamente.";
                $rol = $model->obtenerPorId($id);
            } else {
                $errores[] = "Error al actualizar el rol.";
            }
        } else {
            if ($model->crear($rol)) {
                $mensaje_exito = "Rol creado correctamente.";
                $rol = [
                    'nombre'  => '',
                    'alcance' => 'Acceso limitado a algunos módulos.',
                ];
            } else {
                $errores[] = "Error al crear el rol.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $editando ? 'Editar' : 'Nuevo'; ?> rol</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1><?= $editando ? 'Editar' : 'Nuevo'; ?> rol</h1>

    <p><a href="roles.php">Volver a roles</a></p>

    <?php if ($errores): ?>
        <ul style="color:red;">
            <?php foreach ($errores as $e): ?>
                <li><?= $e; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($mensaje_exito): ?>
        <p style="color:green;"><?= $mensaje_exito; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Nombre del rol:</label><br>
        <input type="text" name="nombre" value="<?= htmlspecialchars($rol['nombre']); ?>"><br><br>

        <label>Alcance / Permisos (descripción):</label><br>
        <textarea name="alcance" rows="4" cols="60"><?= htmlspecialchars($rol['alcance'] ?? ''); ?></textarea><br><br>

        <button type="submit">Guardar</button>
    </form>
</body>
</html>
