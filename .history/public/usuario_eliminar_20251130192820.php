<?php
// public/usuario_eliminar.php
session_start();

require_once __DIR__ . '/../app/models/Usuario.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (($_SESSION['role_id'] ?? null) != 1) {
    die("Acceso denegado. Solo administradores.");
}

if (!isset($_GET['id'])) {
    header('Location: usuarios.php');
    exit;
}

$id = (int)$_GET['id'];

if ($id == $_SESSION['user_id']) {
    die("No puedes inactivar tu propio usuario.");
}

$model = new Usuario();
$model->eliminar($id);

header('Location: usuarios.php');
exit;
