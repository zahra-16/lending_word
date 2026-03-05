<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Admin - Insert ke Database</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #333; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        button { background: #667eea; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #5568d3; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .step { background: #e7f3ff; padding: 15px; margin: 15px 0; border-left: 4px solid #667eea; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Admin - Insert ke Database</h1>

        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'fix') {
            try {
                require_once __DIR__ . '/app/Database.php';
                $db = Database::getInstance()->getConnection();
                
                echo '<div class="success">✓ Koneksi database berhasil!</div>';
                
                // Hapus admin lama
                $db->exec("DELETE FROM admin_users WHERE username = 'admin'");
                echo '<div class="info">→ Admin lama dihapus</div>';
                
                // Insert admin baru
                $password = password_hash('admin123', PASSWORD_BCRYPT);
                $stmt = $db->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
                $stmt->execute(['admin', $password]);
                echo '<div class="info">→ Admin baru diinsert</div>';
                
                // Verifikasi
                $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
                $stmt->execute(['admin']);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify('admin123', $user['password'])) {
                    echo '<div class="success">';
                    echo '<h2>✓✓✓ BERHASIL!</h2>';
                    echo '<p><strong>Admin berhasil dibuat dan diverifikasi!</strong></p>';
                    echo '<p>Username: <strong>admin</strong></p>';
                    echo '<p>Password: <strong>admin123</strong></p>';
                    echo '<p><a href="/lending_word/admin/login.php" style="color: #155724; font-weight: bold;">→ Login Sekarang</a></p>';
                    echo '</div>';
                } else {
                    echo '<div class="error">✗ Verifikasi password gagal!</div>';
                }
                
            } catch (Exception $e) {
                echo '<div class="error">';
                echo '<h3>✗ Error: ' . htmlspecialchars($e->getMessage()) . '</h3>';
                
                if (strpos($e->getMessage(), 'could not find driver') !== false) {
                    echo '<h4>Solusi: Enable PostgreSQL Extension</h4>';
                    echo '<ol>';
                    echo '<li>Buka Laragon → Menu → PHP → php.ini</li>';
                    echo '<li>Cari dan uncomment (hapus ; di depan):<br><code>extension=pdo_pgsql</code><br><code>extension=pgsql</code></li>';
                    echo '<li>Save dan restart Laragon</li>';
                    echo '<li>Refresh halaman ini</li>';
                    echo '</ol>';
                    echo '<p><a href="ENABLE_POSTGRESQL.md" target="_blank">→ Lihat Panduan Lengkap</a></p>';
                } elseif (strpos($e->getMessage(), 'does not exist') !== false) {
                    echo '<h4>Solusi: Buat Database/Tabel</h4>';
                    echo '<ol>';
                    echo '<li>Buka pgAdmin</li>';
                    echo '<li>Buat database: <code>landing_cms</code></li>';
                    echo '<li>Jalankan file <code>setup.sql</code></li>';
                    echo '<li>Refresh halaman ini</li>';
                    echo '</ol>';
                }
                echo '</div>';
            }
        } else {
        ?>

        <div class="warning">
            <h3>⚠ Sebelum Klik Tombol, Pastikan:</h3>
            <ol>
                <li><strong>PostgreSQL extension aktif</strong> di php.ini</li>
                <li><strong>Database landing_cms</strong> sudah dibuat</li>
                <li><strong>Tabel admin_users</strong> sudah dibuat (jalankan setup.sql)</li>
            </ol>
        </div>

        <div class="step">
            <h3>Langkah 1: Enable PostgreSQL Extension</h3>
            <p>Buka: <code>Laragon → Menu → PHP → php.ini</code></p>
            <p>Uncomment baris:</p>
            <pre>extension=pdo_pgsql
extension=pgsql</pre>
            <p>Save dan restart Laragon</p>
        </div>

        <div class="step">
            <h3>Langkah 2: Setup Database</h3>
            <p>Buka pgAdmin, jalankan file <code>setup.sql</code></p>
            <p>Atau jalankan di psql:</p>
            <pre>psql -U postgres -f setup.sql</pre>
        </div>

        <div class="step">
            <h3>Langkah 3: Fix Admin</h3>
            <form method="GET">
                <input type="hidden" name="action" value="fix">
                <button type="submit">🔧 Fix Admin Sekarang</button>
            </form>
        </div>

        <div class="info">
            <h3>📋 Alternatif: Manual via pgAdmin</h3>
            <p>Jika tombol di atas tidak bekerja, jalankan SQL ini di pgAdmin:</p>
            <pre>DELETE FROM admin_users WHERE username = 'admin';

INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

SELECT * FROM admin_users;</pre>
        </div>

        <?php } ?>
    </div>
</body>
</html>
