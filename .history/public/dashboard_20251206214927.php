<?php
// public/dashboard.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$nombreUsuario = $_SESSION['user_name'] ?? 'Usuario';
$roleId        = $_SESSION['role_id'] ?? null;

// Helpers de rol
$esAdmin     = ($roleId == 1);
$esAgente    = ($roleId == 2);
$esEstudiante= ($roleId == 3);

$nombreRol = 'Usuario';
if ($esAdmin) {
    $nombreRol = 'Administrador';
} elseif ($esAgente) {
    $nombreRol = 'Agente de TI';
} elseif ($esEstudiante) {
    $nombreRol = 'Estudiante';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mesa de Ayuda - Panel principal</title>
    <link rel="stylesheet" href="../assets/css/stye.css">
</head>
<body>
    <h1>Mesa de Ayuda - Panel principal</h1>

    <p>
        Bienvenido, <strong><?= htmlspecialchars($nombreUsuario); ?></strong>
        (<em><?= htmlspecialchars($nombreRol); ?></em>)
    </p>

    <p>
        <!-- Opciones comunes -->
        <a href="tickets.php">Tickets</a>

        <!-- Solo ESTUDIANTE puede crear tickets -->
        <?php if ($esEstudiante): ?>
            | <a href="ticket_nuevo.php">Crear Ticket</a>
        <?php endif; ?>

        <!-- Admin: configuración y administración -->
        <?php if ($esAdmin): ?>
            | <a href="tipos_ticket.php">Tipos de Ticket</a>
            | <a href="reportes.php">Reportes</a>
            | <a href="usuarios.php">Usuarios del sistema</a>
            | <a href="roles.php">Roles</a>
        <?php endif; ?>

        <!-- Agente: puede ver reportes -->
        <?php if ($esAgente): ?>
            | <a href="reportes.php">Reportes</a>
        <?php endif; ?>

        | <a href="perfil.php">Mi perfil</a>
        | <a href="public_home.php">Página pública</a>
        | <a href="logout.php">Cerrar sesión</a>
    </p>

    <hr>

    <h2>Resumen rápido</h2>

    <?php if ($esAdmin): ?>
        <p>
            Como <strong>Administrador</strong> puedes:
        </p>
        <ul>
            <li>Administrar usuarios del sistema y sus roles.</li>
            <li>Configurar los tipos de ticket y categorías.</li>
            <li>Visualizar reportes y estadísticas de tickets.</li>
            <li>Consultar todos los tickets registrados.</li>
        </ul>
    <?php elseif ($esAgente): ?>
        <p>
            Como <strong>Agente de TI</strong> puedes:
        </p>
        <ul>
            <li>Ver y gestionar los tickets asignados desde el listado de tickets.</li>
            <li>Registrar la solución antes de culminar un ticket.</li>
            <li>Consultar reportes y estadísticas de tickets.</li>
        </ul>
    <?php elseif ($esEstudiante): ?>
        <p>
            Como <strong>Estudiante</strong> puedes:
        </p>
        <ul>
            <li>Crear nuevos tickets indicando el tipo de solicitud.</li>
            <li>Ver el estado de tus tickets y las respuestas del agente.</li>
            <li>Llenar la encuesta de satisfacción cuando el ticket se marca como culminado.</li>
            <li>Actualizar tu contraseña desde el módulo de perfil.</li>
        </ul>
    <?php else: ?>
        <p>Tu rol no está correctamente configurado. Contacta al administrador del sistema.</p>
    <?php endif; ?>
</body>
</html>
