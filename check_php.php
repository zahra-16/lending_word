<?php
echo "=== PHP Configuration ===\n\n";
echo "PHP Version: " . phpversion() . "\n\n";

echo "=== PDO Drivers ===\n";
$drivers = PDO::getAvailableDrivers();
if (empty($drivers)) {
    echo "⚠ Tidak ada PDO driver tersedia!\n";
} else {
    foreach ($drivers as $driver) {
        echo "✓ $driver\n";
    }
}

echo "\n=== PostgreSQL Extension ===\n";
if (extension_loaded('pgsql')) {
    echo "✓ pgsql extension loaded\n";
} else {
    echo "✗ pgsql extension NOT loaded\n";
}

if (extension_loaded('pdo_pgsql')) {
    echo "✓ pdo_pgsql extension loaded\n";
} else {
    echo "✗ pdo_pgsql extension NOT loaded\n";
    echo "\n⚠ SOLUSI:\n";
    echo "1. Buka php.ini\n";
    echo "2. Uncomment (hapus ;) pada baris:\n";
    echo "   ;extension=pdo_pgsql\n";
    echo "   ;extension=pgsql\n";
    echo "3. Restart Apache/Laragon\n";
}

echo "\n=== Loaded Extensions ===\n";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    if (stripos($ext, 'pdo') !== false || stripos($ext, 'pgsql') !== false) {
        echo "✓ $ext\n";
    }
}
