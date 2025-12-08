<?php
session_start();
require_once __DIR__ . '/../app/core/Validator.php';
require_once __DIR__ . '/../app/models/Colaborador.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$model = new Colaborador();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editando = $id > 0;

$errores = [];
$mensaje_exito = '';
$colaborador = [
    'primer_nombre'   => '',
    'segundo_nombre'  => '',
    'primer_apellido' => '',
    'segundo_apellido'=> '',
    'identificacion'  => '',
    'email'           => '',
    'telefono'        => '',
    'tipo'            => 'ESTUDIANTE',
];

if ($editando) {
    $colaboradorBD = $model->obtenerPorId($id);
    if ($colaboradorBD) {
        $colaborador = $colaboradorBD;
    } else {
        die("Colaborador no encontrado");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $colaborador['primer_nombre']   = Validator::sanitizeString($_POST['primer_nombre'] ?? '');
    $colaborador['segundo_nombre']  = Validator::sanitizeString($_POST['segundo_nombre'] ?? '');
    $colaborador['primer_apellido'] = Validator::sanitizeString($_POST['primer_apellido'] ?? '');
    $colaborador['segundo_apellido']= Validator::sanitizeString($_POST['segundo_apellido'] ?? '');
    $colaborador['identificacion']  = Validator::sanitizeString($_POST['identificacion'] ?? '');
    $colaborador['email']           = Validator::sanitizeEmail($_POST['email'] ?? '');
    $colaborador['telefono']        = Validator::sanitizeString($_POST['telefono'] ?? '');
    $colaborador['tipo']            = $_POST['tipo'] ?? 'ESTUDIANTE';

    if (!Validator::required($colaborador['primer_nombre'])) {
        $errores[] = "El primer nombre es obligatorio.";
    }
    if (!Validator::required($colaborador['primer_apellido'])) {
        $errores[] = "El primer apellido es obligatorio.";
    }
    if (!Validator::required($colaborador['identificacion'])) {
        $errores[] = "La identificación es obligatoria.";
    }
    if (!Validator::isEmail($colaborador['email'])) {
        $errores[] = "El email no es válido.";
    }

    if (empty($errores)) {
        if ($editando) {
            if ($model->actualizar($id, $colaborador)) {
                $mensaje_exito = "Colaborador actualizado correctamente.";
            } else {
                $errores[] = "Error al actualizar.";
            }
        } else {
            if ($model->crear($colaborador)) {
                $mensaje_exito = "Colaborador creado correctamente.";
                $colaborador = [
                    'primer_nombre'   => '',
                    'segundo_nombre'  => '',
                    'primer_apellido' => '',
                    'segundo_apellido'=> '',
                    'identificacion'  => '',
                    'email'           => '',
                    'telefono'        => '',
                    'tipo'            => 'ESTUDIANTE',
                ];
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
    <title><?= $editando ? 'Editar' : 'Nuevo'; ?> colaborador</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1><?= $editando ? 'Editar' : 'Nuevo'; ?> colaborador</h1>

    <p><a href="colaboradores.php">Volver al listado</a></p>

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
        <label>Primer nombre:</label><br>
        <input type="text" name="primer_nombre" value="<?= $colaborador['primer_nombre']; ?>"><br><br>

        <label>Segundo nombre:</label><br>
        <input type="text" name="segundo_nombre" value="<?= $colaborador['segundo_nombre']; ?>"><br><br>

        <label>Primer apellido:</label><br>
        <input type="text" name="primer_apellido" value="<?= $colaborador['primer_apellido']; ?>"><br><br>

        <label>Segundo apellido:</label><br>
        <input type="text" name="segundo_apellido" value="<?= $colaborador['segundo_apellido']; ?>"><br><br>

        <label>Identificación:</label><br>
        <input type="text" name="identificacion" value="<?= $colaborador['identificacion']; ?>"><br><br>

        <label>Email:</label><br>
        <input type="text" name="email" value="<?= $colaborador['email']; ?>"><br><br>

        <label>Teléfono:</label><br>
        <input type="text" name="telefono" value="<?= $colaborador['telefono']; ?>"><br><br>

        <label>Tipo:</label><br>
        <select name="tipo">
            <option value="ESTUDIANTE" <?= $colaborador['tipo'] == 'ESTUDIANTE' ? 'selected' : ''; ?>>
                Estudiante
            </option>
            <option value="COLABORADOR" <?= $colaborador['tipo'] == 'COLABORADOR' ? 'selected' : ''; ?>>
                Colaborador
            </option>
        </select>
        <br><br>

        <button type="submit">Guardar</button>
    </form>
</body>
</html>
