<?php
// Script untuk insert admin langsung ke database
require_once __DIR__ . '/app/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "✓ Koneksi database berhasil!\n\n";
    
    // Cek apakah tabel ada
    $stmt = $db->query("SELECT COUNT(*) FROM admin_users");
    $count = $stmt->fetchColumn();
    echo "Jumlah admin saat ini: $count\n\n";
    
    // Hapus admin lama
    $db->exec("DELETE FROM admin_users WHERE username = 'admin'");
    echo "✓ Admin lama dihapus\n";
    
    // Insert admin baru
    $password = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
    $stmt->execute(['admin', $password]);
    echo "✓ Admin baru berhasil dibuat!\n\n";
    
    // Verifikasi
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "=== Data Admin ===\n";
        echo "ID: {$user['id']}\n";
        echo "Username: {$user['username']}\n";
        echo "Password Hash: {$user['password']}\n\n";
        
        // Test password
        if (password_verify('admin123', $user['password'])) {
            echo "✓✓✓ Password 'admin123' BERHASIL diverifikasi!\n";
            echo "\nSekarang coba login dengan:\n";
            echo "Username: admin\n";
            echo "Password: admin123\n";
        } else {
            echo "✗ Password verify gagal!\n";
        }
    } else {
        echo "✗ Admin tidak ditemukan setelah insert!\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "\nPastikan:\n";
    echo "1. PostgreSQL service running\n";
    echo "2. Database 'landing_cms' sudah dibuat\n";
    echo "3. Tabel 'admin_users' sudah dibuat (jalankan setup.sql)\n";
    echo "4. Extension pdo_pgsql aktif di php.ini\n";
}
