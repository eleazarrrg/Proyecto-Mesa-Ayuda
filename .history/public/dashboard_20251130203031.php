<?php
// public/dashboard.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$nombreUsuario = $_SESSION['user_name'] ?? 'Usuario';
$roleId        = $_SESSION['role_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mesa de Ayuda - Panel principal</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Mesa de Ayuda - Panel principal</h1>

    <p>Bienvenido, <strong><?= htmlspecialchars($nombreUsuario); ?></strong></p>

    <p>
        <a href="tickets.php">Tickets</a> |
        <a href="ticket_nuevo.php">Crear Ticket</a> |
        <a href="colaboradores.php">Colaboradores / Estudiantes</a> |
        <a href="tipos_ticket.php">Tipos de Ticket</a> |
        <a href="reportes.php">Reportes</a> |
        <a href="perfil.php">Mi perfil / Cambiar contraseña</a>
        <?php if ($roleId == 1): ?>
            | <a href="usuarios.php">Usuarios del sistema</a>
            | <a href="roles.php">Roles</a>
        <?php endif; ?>
        | <a href="public_home.php">Página pública</a> |
        <a href="logout.php">Cerrar sesión</a>
    </p>

    <hr>

    <h2>Resumen rápido</h2>
    <p>Desde este panel puedes acceder a los principales módulos del sistema de Mesa de Ayuda.</p>
</body>
</html>
