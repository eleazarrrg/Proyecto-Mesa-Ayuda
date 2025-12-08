<?php
// public/rol_eliminar.php
session_start();

require_once __DIR__ . '/../app/models/Rol.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
if (($_SESSION['role_id'] ?? null) != 1) {
    die("Acceso denegado. Solo administradores.");
}

if (!isset($_GET['id'])) {
    header('Location: roles.php');
    exit;
}

$id = (int)$_GET['id'];
if ($id === 1) {
    die("No se puede eliminar el rol administrador.");
}

$model = new Rol();
$model->eliminar($id);

header('Location: roles.php');
exit;
