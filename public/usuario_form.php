<?php
// public/usuario_form.php
session_start();

require_once __DIR__ . '/../app/core/Validator.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/models/Usuario.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (($_SESSION['role_id'] ?? null) != 1) {
    die("Acceso denegado. Solo administradores.");
}

$usuarioModel = new Usuario();
$db = Database::getInstance()->getConnection();

// roles para el select
$roles = $db->query("SELECT id, nombre FROM roles ORDER BY id")->fetchAll();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editando = $id > 0;

$errores = [];
$mensaje_exito = '';

$usuario = [
    'nombre'   => '',
    'username' => '',
    'email'    => '',
    'rol_id'   => 2,
    'activo'   => 1,
];

if ($editando) {
    $uDB = $usuarioModel->obtenerPorId($id);
    if ($uDB) {
        $usuario = $uDB;
    } else {
        die("Usuario no encontrado.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario['nombre']   = Validator::sanitizeString($_POST['nombre']   ?? '');
    $usuario['username'] = Validator::sanitizeString($_POST['username'] ?? '');
    $usuario['email']    = Validator::sanitizeEmail($_POST['email']     ?? '');
    $usuario['rol_id']   = (int)($_POST['rol_id'] ?? 2);
    $usuario['activo']   = isset($_POST['activo']) ? 1 : 0;

    $pass1 = $_POST['password']        ?? '';
    $pass2 = $_POST['password_repeat'] ?? '';

    if (!Validator::required($usuario['nombre'])) {
        $errores[] = "El nombre es obligatorio.";
    }
    if (!Validator::required($usuario['username'])) {
        $errores[] = "El usuario es obligatorio.";
    }
    if (!Validator::isEmail($usuario['email'])) {
        $errores[] = "El email no es válido.";
    }

    // contraseña obligatoria solo cuando se crea
    $cambiarPassword = false;
    if ($editando) {
        if (!empty($pass1) || !empty($pass2)) {
            $cambiarPassword = true;
            if ($pass1 !== $pass2) {
                $errores[] = "Las contraseñas no coinciden.";
            } elseif (strlen($pass1) < 8) {
                $errores[] = "La contraseña debe tener al menos 8 caracteres.";
            }
        }
    } else {
        if (!Validator::required($pass1) || !Validator::required($pass2)) {
            $errores[] = "La contraseña y su repetición son obligatorias.";
        } elseif ($pass1 !== $pass2) {
            $errores[] = "Las contraseñas no coinciden.";
        } elseif (strlen($pass1) < 8) {
            $errores[] = "La contraseña debe tener al menos 8 caracteres.";
        } else {
            $cambiarPassword = true;
        }
    }

    if (empty($errores)) {
        if ($editando) {
            if ($usuarioModel->actualizar($id, $usuario)) {
                if ($cambiarPassword) {
                    $hash = password_hash($pass1, PASSWORD_DEFAULT);
                    $usuarioModel->actualizarPassword($id, $hash);
                }
                $mensaje_exito = "Usuario actualizado correctamente.";
                $usuario = $usuarioModel->obtenerPorId($id);
            } else {
                $errores[] = "Error al actualizar el usuario.";
            }
        } else {
            $hash = password_hash($pass1, PASSWORD_DEFAULT);
            $data = $usuario;
            $data['password_hash'] = $hash;

            if ($usuarioModel->crear($data)) {
                $mensaje_exito = "Usuario creado correctamente.";
                $usuario = [
                    'nombre'   => '',
                    'username' => '',
                    'email'    => '',
                    'rol_id'   => 2,
                    'activo'   => 1,
                ];
            } else {
                $errores[] = "Error al crear el usuario.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $editando ? 'Editar' : 'Nuevo'; ?> usuario</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1><?= $editando ? 'Editar' : 'Nuevo'; ?> usuario</h1>

    <p>
        <a href="dashboard.php">Inicio</a> |
        <a href="usuarios.php">Volver al listado</a>
    </p>

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
        <label>Nombre completo:</label><br>
        <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']); ?>"><br><br>

        <label>Usuario (login):</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($usuario['username']); ?>"><br><br>

        <label>Email:</label><br>
        <input type="text" name="email" value="<?= htmlspecialchars($usuario['email']); ?>"><br><br>

        <label>Rol:</label><br>
        <select name="rol_id">
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id']; ?>"
                    <?= $usuario['rol_id'] == $r['id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($r['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>
            <input type="checkbox" name="activo" value="1" <?= $usuario['activo'] ? 'checked' : ''; ?>>
            Usuario activo
        </label>
        <br><br>

        <hr>

        <h3>Contraseña</h3>
        <?php if ($editando): ?>
            <p>Si dejas estos campos vacíos, la contraseña no se cambiará.</p>
        <?php endif; ?>

        <label>Contraseña:</label><br>
        <input type="password" name="password"><br><br>

        <label>Repetir contraseña:</label><br>
        <input type="password" name="password_repeat"><br><br>

        <button type="submit">Guardar</button>
    </form>
</body>
</html>
