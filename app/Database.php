<?php
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $host = 'localhost';
        $port = '5432';
        $dbname = 'landing_cms';
        $user = 'postgres';
        $pass = '123455';
        
        try {
            $this->pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
}
