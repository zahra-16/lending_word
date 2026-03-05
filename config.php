<?php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'landing_cms');
define('DB_USER', 'postgres');
define('DB_PASS', '123455');

try {
    $pdo = new PDO("pgsql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
