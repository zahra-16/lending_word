<?php
require_once __DIR__ . '/../Database.php';

class FeaturedVehicle {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists() {
        // Detect driver: PostgreSQL uses SERIAL, MySQL uses AUTO_INCREMENT
        $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'pgsql') {
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS featured_vehicles (
                    id SERIAL PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    subtitle VARCHAR(255) DEFAULT '',
                    badge VARCHAR(100) DEFAULT '',
                    image VARCHAR(500) NOT NULL,
                    model_variant_id INT DEFAULT NULL,
                    link VARCHAR(500) DEFAULT '',
                    is_active SMALLINT DEFAULT 1,
                    sort_order INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        } else {
            // MySQL / MariaDB
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS featured_vehicles (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    subtitle VARCHAR(255) DEFAULT '',
                    badge VARCHAR(100) DEFAULT '',
                    image VARCHAR(500) NOT NULL,
                    model_variant_id INT DEFAULT NULL,
                    link VARCHAR(500) DEFAULT '',
                    is_active TINYINT(1) DEFAULT 1,
                    sort_order INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        }
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM featured_vehicles ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActive() {
        $stmt = $this->db->query("SELECT * FROM featured_vehicles WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM featured_vehicles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO featured_vehicles (name, subtitle, badge, image, model_variant_id, link, is_active, sort_order)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['name'],
            $data['subtitle'] ?? '',
            $data['badge'] ?? '',
            $data['image'],
            !empty($data['model_variant_id']) ? (int)$data['model_variant_id'] : null,
            $data['link'] ?? '',
            isset($data['is_active']) ? 1 : 0,
            (int)($data['sort_order'] ?? 0),
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE featured_vehicles
            SET name=?, subtitle=?, badge=?, image=?, model_variant_id=?, link=?, is_active=?, sort_order=?
            WHERE id=?
        ");
        return $stmt->execute([
            $data['name'],
            $data['subtitle'] ?? '',
            $data['badge'] ?? '',
            $data['image'],
            !empty($data['model_variant_id']) ? (int)$data['model_variant_id'] : null,
            $data['link'] ?? '',
            isset($data['is_active']) ? 1 : 0,
            (int)($data['sort_order'] ?? 0),
            (int)$id,
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM featured_vehicles WHERE id = ?");
        return $stmt->execute([$id]);
    }
}