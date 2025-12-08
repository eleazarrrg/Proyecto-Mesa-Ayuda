<?php
session_start();

require_once __DIR__ . '/../app/core/Validator.php';
require_once __DIR__ . '/../app/models/Usuario.php';
require_once __DIR__ . '/../app/models/Estudiante.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$usuarioModel    = new Usuario();
$estudianteModel = new Estudiante();

$userId  = $_SESSION['user_id'];
$usuario = $usuarioModel->obtenerPorId($userId);

if (!$usuario) {
    // Si no encuentra el usuario, lo regresamos al login
    session_destroy();
    header('Location: index.php');
    exit;
}

// Buscar perfil de estudiante (por email)
$fotoPerfil = null;
$estudiante = null;

if (!empty($usuario['email'])) {
    $estudiante = $estudianteModel->obtenerPorEmail($usuario['email']);
    if ($estudiante && !empty($estudiante['foto_perfil'])) {
        // En registro guardamos rutas como "assets/uploads/archivo.jpg"
        // Desde /public hay que subir un nivel: "../assets/uploads/archivo.jpg"
        $fotoPerfil = '../' . ltrim($estudiante['foto_perfil'], '/');
    }
}

$errores = [];
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual = $_POST['password_actual'] ?? '';
    $nueva  = $_POST['password_nueva'] ?? '';
    $repite = $_POST['password_repite'] ?? '';

    if (!Validator::required($actual) ||
        !Validator::required($nueva) ||
        !Validator::required($repite)) {
        $errores[] = "Todos los campos son obligatorios.";
    } else {
        if (!password_verify($actual, $usuario['password_hash'])) {
            $errores[] = "La contraseña actual no es correcta.";
        }

        if ($nueva !== $repite) {
            $errores[] = "La nueva contraseña y la repetición no coinciden.";
        }

        if (strlen($nueva) < 8) {
            $errores[] = "La nueva contraseña debe tener al menos 8 caracteres.";
        }
    }

    if (empty($errores)) {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        if ($usuarioModel->actualizarPassword($userId, $hash)) {
            $mensaje_exito = "Contraseña actualizada correctamente.";
            // Refrescamos los datos
            $usuario = $usuarioModel->obtenerPorId($userId);

            // Volvemos a cargar el estudiante y la foto
            if (!empty($usuario['email'])) {
                $estudiante = $estudianteModel->obtenerPorEmail($usuario['email']);
                if ($estudiante && !empty($estudiante['foto_perfil'])) {
                    $fotoPerfil = '../' . ltrim($estudiante['foto_perfil'], '/');
                } else {
                    $fotoPerfil = null;
                }
            }
        } else {
            $errores[] = "Error al actualizar la contraseña.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Mi perfil</h1>

    <p><a href="dashboard.php">Volver al inicio</a></p>

    <?php if ($fotoPerfil): ?>
        <div class="perfil-foto-wrapper">
            <img
                src="<?= htmlspecialchars($fotoPerfil, ENT_QUOTES, 'UTF-8'); ?>"
                alt="Foto de perfil"
                class="perfil-foto"
            >
        </div>
    <?php endif; ?>

    <h3>Datos</h3>
    <p>Nombre: <?= htmlspecialchars($usuario['nombre']); ?></p>
    <p>Usuario: <?= htmlspecialchars($usuario['username']); ?></p>
    <p>Email: <?= htmlspecialchars($usuario['email']); ?></p>

    <h3>Cambiar contraseña</h3>

    <?php if (!empty($errores)): ?>
        <ul style="color:red;">
            <?php foreach ($errores as $e): ?>
                <li><?= htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($mensaje_exito): ?>
        <p style="color:green;"><?= htmlspecialchars($mensaje_exito); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Contraseña actual:</label><br>
        <input type="password" name="password_actual"><br><br>

        <label>Nueva contraseña:</label><br>
        <input type="password" name="password_nueva"><br><br>

        <label>Repetir nueva contraseña:</label><br>
        <input type="password" name="password_repite"><br><br>

        <button type="submit">Actualizar contraseña</button>
    </form>
</body>
</html>
