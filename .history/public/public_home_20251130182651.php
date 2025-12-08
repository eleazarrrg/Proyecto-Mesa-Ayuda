<?php
require_once __DIR__ . '/../app/core/Database.php';

$db = Database::getInstance()->getConnection();
$tipos = $db->query("SELECT nombre, categoria FROM tipos_ticket ORDER BY nombre")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mesa de Ayuda - Página pública</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Mesa de Ayuda - UTP (Página Pública)</h1>

    <p>
        Este sistema permite a estudiantes y colaboradores registrar incidentes
        y solicitudes académicas o técnicas para que el equipo de soporte las atienda.
    </p>

    <h2>Tipos de Tickets disponibles</h2>
    <ul>
        <?php foreach ($tipos as $t): ?>
            <li>
                <?= $t['nombre']; ?> (<?= $t['categoria']; ?>)
            </li>
        <?php endforeach; ?>
    </ul>

    <p>
        Si eres parte del equipo de soporte, ingresa al sistema:
        <a href="index.php">Iniciar sesión</a>
    </p>
</body>
</html>
