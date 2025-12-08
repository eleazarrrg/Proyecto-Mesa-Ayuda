<?php
session_start();
require_once __DIR__ . '/../app/models/TipoTicket.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: tipos_ticket.php');
    exit;
}

$id = (int)$_GET['id'];
$model = new TipoTicket();
$model->eliminar($id);

header('Location: tipos_ticket.php');
exit;
