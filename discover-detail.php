<?php
// ================================================================
// LETAKKAN FILE INI DI: C:\laragon\www\lending_word\discover-detail.php
// (ROOT — sejajar dengan finder.php, index.php, models.php)
// ================================================================

// Salin PERSIS baris require_once dari index.php root kamu
// Buka C:\laragon\www\lending_word\index.php dan cari barisnya
require_once __DIR__ . '/app/database.php';

// Ambil koneksi dari singleton Database class
$pdo = Database::getInstance()->getConnection();

// Ambil data dari DB
try {
    $stmt = $pdo->query("SELECT * FROM discover_features ORDER BY sort_order ASC, id ASC");
    $discoverFeatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $discoverFeatures = [];
}

$features_json = json_encode($discoverFeatures, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);

// Load view (pastikan sudah hapus require_once di baris pertama file view itu)
include __DIR__ . '/app/views/frontend/discover-detail.php';