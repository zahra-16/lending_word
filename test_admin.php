<?php
// Test koneksi database dan cek admin user
require_once __DIR__ . '/app/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "✓ Koneksi database berhasil!\n\n";
    
    // Cek tabel admin_users
    $stmt = $db->query("SELECT * FROM admin_users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== Data Admin Users ===\n";
    if (empty($users)) {
        echo "⚠ Tidak ada data admin!\n\n";
        
        // Insert admin baru
        echo "Membuat admin baru...\n";
        $password = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?) ON CONFLICT (username) DO UPDATE SET password = EXCLUDED.password");
        $stmt->execute(['admin', $password]);
        echo "✓ Admin berhasil dibuat!\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    } else {
        foreach ($users as $user) {
            echo "ID: {$user['id']}\n";
            echo "Username: {$user['username']}\n";
            echo "Password Hash: {$user['password']}\n\n";
            
            // Test password
            if (password_verify('admin123', $user['password'])) {
                echo "✓ Password 'admin123' COCOK untuk user '{$user['username']}'\n";
            } else {
                echo "✗ Password 'admin123' TIDAK COCOK!\n";
                echo "Updating password...\n";
                $newHash = password_hash('admin123', PASSWORD_BCRYPT);
                $stmt = $db->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                $stmt->execute([$newHash, $user['id']]);
                echo "✓ Password berhasil diupdate!\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
