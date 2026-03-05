<?php
require_once __DIR__ . '/app/controllers/FinderController.php';

$id = $_GET['id'] ?? 0;
$controller = new FinderController();
$controller->detail($id);