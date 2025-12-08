<?php
session_start();
require_once __DIR__ . '/../app/core/Auth.php';

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
    <h1>Mesa de Ayuda - Login</h1>
    <?php if ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Usuario:</label>
        <input type="text" name="username">

        <label>Contraseña:</label>
        <input type="password" name="password">

        <button type="submit">Ingresar</button>
    </form>
</body>
</html>
