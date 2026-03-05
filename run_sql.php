<?php
require_once 'config.php';

try {
    $sql = file_get_contents('database/specification_cards_update.sql');
    $pdo->exec($sql);
    echo "SQL executed successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
