<?php
// public/index.php
session_start();

require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/Validator.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Validator::sanitizeString($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!Validator::required($username) || !Validator::required($password)) {
        $error = 'Debe ingresar usuario y contraseña.';
    } else {
        if (Auth::login($username, $password)) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Credenciales inválidas.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Mesa de Ayuda</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Login - Mesa de Ayuda</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Usuario:</label><br>
        <input type="text" name="username"><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password"><br><br>

        <button type="submit">Ingresar</button>
    </form>

    <br>
    <p>
        ¿Eres estudiante nuevo?
        <a href="registro.php">Regístrate aquí</a>
    </p>

    <hr>
    <p>
        ¿Solo quieres ver información general?
        <a href="public_home.php">Ir a la página pública</a>
    </p>
</body>
</html>
