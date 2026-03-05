<?php
session_start();
require_once __DIR__ . '/../app/models/Admin.php';

$message = '';
$debug = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $debug['input_username'] = $username;
    $debug['input_password'] = $password;
    
    try {
        $adminModel = new Admin();
        $user = $adminModel->login($username, $password);
        
        if ($user) {
            $message = "✓ Login BERHASIL!";
            $debug['user_found'] = $user;
            Admin::setSession($user['id']);
        } else {
            $message = "✗ Login GAGAL - Username atau password salah";
            $debug['user_found'] = false;
        }
    } catch (Exception $e) {
        $message = "✗ Error: " . $e->getMessage();
        $debug['error'] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Login</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #667eea; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #5568d3; }
        .message { padding: 15px; margin: 20px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .debug { background: #e7f3ff; padding: 15px; border-radius: 5px; margin-top: 20px; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .info { background: #fff3cd; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Login Admin</h1>
        
        <div class="info">
            <strong>Default Credentials:</strong><br>
            Username: admin<br>
            Password: admin123
        </div>

        <?php if ($message): ?>
            <div class="message <?= strpos($message, '✓') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="admin" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="text" name="password" value="admin123" required>
            </div>
            <button type="submit">Test Login</button>
        </form>

        <?php if (!empty($debug)): ?>
            <div class="debug">
                <h3>Debug Information:</h3>
                <pre><?= print_r($debug, true) ?></pre>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
            <div class="message success">
                ✓ Session aktif! <a href="/lending_word/admin/">Ke Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
