<?php
require_once __DIR__ . '/../Database.php';

class Admin {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Login admin
    public function login($username, $password) {
    $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}

    
    
    // Check if logged in
    public static function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    // Set session
    public static function setSession($userId) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $userId;
    }
    
    // Destroy session
    public static function logout() {
        session_destroy();
    }
}
