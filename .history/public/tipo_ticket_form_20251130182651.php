<?php
session_start();
require_once __DIR__ . '/../app/core/Validator.php';
require_once __DIR__ . '/../app/models/TipoTicket.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$model = new TipoTicket();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editando = $id > 0;

$errores = [];
$mensaje_exito = '';
$tipo = [
    'nombre' => '',
    'categoria' => 'TECNICO'
];

if ($editando) {
    $tipoBD = $model->obtenerPorId($id);
    if ($tipoBD) {
        $tipo = $tipoBD;
    } else {
        die("Tipo no encontrado");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo['nombre'] = Validator::sanitizeString($_POST['nombre'] ?? '');
    $tipo['categoria'] = $_POST['categoria'] ?? 'TECNICO';

    if (!Validator::required($tipo['nombre'])) {
        $errores[] = "El nombre es obligatorio.";
    }

    if (empty($errores)) {
        if ($editando) {
            if ($model->actualizar($id, $tipo)) {
                $mensaje_exito = "Tipo actualizado.";
            } else {
                $errores[] = "Error al actualizar.";
            }
        } else {
            if ($model->crear($tipo)) {
                $mensaje_exito = "Tipo creado.";
                $tipo = ['nombre' => '', 'categoria' => 'TECNICO'];
            } else {
                $errores[] = "Error al crear.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $editando ? 'Editar' : 'Nuevo'; ?> tipo de ticket</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1><?= $editando ? 'Editar' : 'Nuevo'; ?> tipo de ticket</h1>

    <p><a href="tipos_ticket.php">Volver al listado</a></p>

    <?php if (!empty($errores)): ?>
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
        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="<?= $tipo['nombre']; ?>"><br><br>

        <label>Categoría:</label><br>
        <select name="categoria">
            <option value="TECNICO" <?= $tipo['categoria'] == 'TECNICO' ? 'selected' : ''; ?>>Técnico</option>
            <option value="ACADEMICO" <?= $tipo['categoria'] == 'ACADEMICO' ? 'selected' : ''; ?>>Académico</option>
        </select>
        <br><br>

        <button type="submit">Guardar</button>
    </form>
</body>
</html>
