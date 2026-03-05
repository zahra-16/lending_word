<?php
require_once __DIR__ . '/app/controllers/ContactController.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: /lending_word/finder.php');
    exit;
}

$controller = new ContactController();
$controller->show($id);