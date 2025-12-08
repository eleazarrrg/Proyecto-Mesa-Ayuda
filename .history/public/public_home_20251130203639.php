<?php
require_once __DIR__ . '/../app/core/Database.php';

$db = Database::getInstance()->getConnection();

$tipos = $db->query("SELECT nombre, categoria FROM tipos_ticket ORDER BY nombre")->fetchAll();

$tecnicos = [];
$academicos = [];
foreach ($tipos as $t) {
    if ($t['categoria'] === 'TECNICO') {
        $tecnicos[] = $t['nombre'];
    } else {
        $academicos[] = $t['nombre'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mesa de Ayuda - Página pública</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Mesa de Ayuda - Información General</h1>

    <p>
        Este sistema de <strong>Mesa de Ayuda</strong> permite a estudiantes y colaboradores
        registrar solicitudes y reportar incidentes relacionados con los servicios de TI
        y con procesos académicos. Un sistema helpdesk mejora el tiempo de respuesta,
        centraliza las solicitudes y deja evidencia de todo lo atendido.
    </p>

    <h2>Tipos de Tickets</h2>
    <p>Los tipos de tickets se dividen en dos grandes categorías:</p>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Tickets de Soporte Técnico</th>
            <th>Tickets Académicos</th>
        </tr>
        <tr>
            <td valign="top">
                <p>Ejemplos: problemas con correo institucional, acceso a internet, equipos dañados, sistemas de laboratorio, etc.</p>
                <ul>
                    <?php foreach ($tecnicos as $n): ?>
                        <li><?= htmlspecialchars($n); ?></li>
                    <?php endforeach; ?>
                </ul>
            </td>
            <td valign="top">
                <p>Ejemplos: solicitudes de créditos oficiales, reclamo de notas, certificados, trámites académicos.</p>
                <ul>
                    <?php foreach ($academicos as $n): ?>
                        <li><?= htmlspecialchars($n); ?></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    </table>

    <p>
        Si eres parte del Departamento de TI o un colaborador autorizado, puedes ingresar al sistema:
        <a href="index.php">Iniciar sesión</a>
    </p>
</body>
</html>
