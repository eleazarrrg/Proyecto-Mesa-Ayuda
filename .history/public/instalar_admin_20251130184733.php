<?php
// public/instalar_admin.php
session_start();

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Validator.php';

$db = Database::getInstance()->getConnection();

// Ver si ya hay usuarios
$row = $db->query("SELECT COUNT(*) AS total FROM usuarios")->fetch();
$total = (int)($row['total'] ?? 0);

if ($total > 0) {
    die("Ya existen usuarios en el sistema. El instalador solo es para crear el primer admin. 
    Por seguridad, borra este archivo (instalar_admin.php).");
}

$errores = [];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = Validator::sanitizeString($_POST['nombre']   ?? '');
    $username = Validator::sanitizeString($_POST['username'] ?? '');
    $email    = Validator::sanitizeEmail($_POST['email']     ?? '');
    $pass1    = $_POST['password']        ?? '';
    $pass2    = $_POST['password_repeat'] ?? '';

    if (!Validator::required($nombre))   $errores[] = "El nombre es obligatorio.";
    if (!Validator::required($username)) $errores[] = "El usuario es obligatorio.";
    if (!Validator::isEmail($email))     $errores[] = "El email no es válido.";
    if (!Validator::required($pass1) || !Validator::required($pass2)) {
        $errores[] = "La contraseña y su repetición son obligatorias.";
    } elseif ($pass1 !== $pass2) {
        $errores[] = "Las contraseñas no coinciden.";
    } elseif (strlen($pass1) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    if (empty($errores)) {
        $hash = password_hash($pass1, PASSWORD_DEFAULT);

        // rol_id = 1 asumimos que es ADMIN
        $sql = "INSERT INTO usuarios (nombre, username, email, password_hash, rol_id, activo)
                VALUES (:nombre, :username, :email, :password_hash, :rol_id, 1)";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([
            ':nombre'        => $nombre,
            ':username'      => $username,
            ':email'         => $email,
            ':password_hash' => $hash,
            ':rol_id'        => 1, // ADMIN
        ]);

        if ($ok) {
            $mensaje = "Usuario administrador creado correctamente. 
                        Ya puedes ir a index.php e iniciar sesión.";
        } else {
            $errores[] = "Error al crear el usuario en la base de datos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Instalar administrador - Mesa de Ayuda</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Instalador de Administrador</h1>

    <p><strong>Uso:</strong> este archivo sirve solo para crear el <u>primer usuario admin</u> del sistema.
       Después de usarlo y crear el admin, <b>borra este archivo</b> por seguridad.</p>

    <?php if (!empty($errores)): ?>
        <ul style="color:red;">
            <?php foreach ($errores as $e): ?>
                <li><?= $e; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($mensaje): ?>
        <p style="color:green;"><?= $mensaje; ?></p>
        <p><a href="index.php">Ir a la pantalla de login</a></p>
    <?php endif; ?>

    <?php if (!$mensaje): ?>
    <form method="POST">
        <label>Nombre completo:</label><br>
        <input type="text" name="nombre"><br><br>

        <label>Usuario (login):</label><br>
        <input type="text" name="username"><br><br>

        <label>Email:</label><br>
        <input type="text" name="email"><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password"><br><br>

        <label>Repetir contraseña:</label><br>
        <input type="password" name="password_repeat"><br><br>

        <button type="submit">Crear administrador</button>
    </form>
    <?php endif; ?>
</body>
</html>
