<?php
// public/dashboard.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$nombreUsuario = $_SESSION['user_name'] ?? 'Usuario';
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
        <a href="perfil.php">Mi perfil / Cambiar contraseña</a> |
        <a href="public_home.php">Página pública</a> |
        <a href="logout.php">Cerrar sesión</a>
    </p>

    <hr>

    <h2>Resumen rápido</h2>
    <p>Desde este panel puedes acceder a:</p>
    <ul>
        <li><strong>Tickets:</strong> crear, listar y dar seguimiento a los incidentes.</li>
        <li><strong>Colaboradores / Estudiantes:</strong> registrar quiénes crean tickets.</li>
        <li><strong>Tipos de Ticket:</strong> configurar los tipos técnicos y académicos.</li>
        <li><strong>Reportes:</strong> ver tickets por estado, tipo, agente y exportar a Excel.</li>
        <li><strong>Mi perfil:</strong> actualizar tu contraseña de acceso al sistema.</li>
    </ul>
</body>
</html>
