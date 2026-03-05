<?php
require_once __DIR__ . '/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>Categories:</h2>";
    $stmt = $db->query("SELECT * FROM model_categories ORDER BY id");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($categories);
    echo "</pre>";
    
    echo "<h2>Variants Count by Category ID:</h2>";
    $stmt = $db->query("SELECT category_id, COUNT(*) as count FROM model_variants GROUP BY category_id ORDER BY category_id");
    $counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($counts);
    echo "</pre>";
    
    echo "<h2>Sample Variants:</h2>";
    $stmt = $db->query("SELECT id, category_id, name FROM model_variants ORDER BY category_id LIMIT 20");
    $variants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($variants);
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
