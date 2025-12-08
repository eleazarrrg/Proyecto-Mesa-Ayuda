<?php
// public/index.php
session_start();

require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/Validator.php';

// Si ya está logueado, mandarlo al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Validator::sanitizeString($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!Validator::required($username) || !Validator::required($password)) {
        $error = "Usuario y contraseña son obligatorios.";
    } else {
        if (Auth::login($username, $password)) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Credenciales inválidas.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mesa de Ayuda - Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Mesa de Ayuda - Iniciar sesión</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Usuario:</label><br>
        <input type="text" name="username"><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password"><br><br>

        <button type="submit">Ingresar</button>
    </form>

    <br>
    <hr>
    <p>
        ¿Solo quieres ver información general?
        <a href="public_home.php">Ir a la página pública</a>
    </p>
</body>
</html>
