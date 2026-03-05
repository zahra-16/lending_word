<?php
session_start();
require_once __DIR__ . '/app/controllers/FrontendController.php';

$controller = new FrontendController();
$controller->index();
