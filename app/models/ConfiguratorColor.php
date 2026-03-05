<?php
require_once __DIR__ . '/../Database.php';

class ConfiguratorColor {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM configurator_colors ORDER BY sort_order ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByType($type) {
        $stmt = $this->db->prepare("SELECT * FROM configurator_colors WHERE type = ? ORDER BY sort_order ASC");
        $stmt->execute([$type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM configurator_colors WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO configurator_colors (name, hex_code, type, price, image, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['name'], $data['hex_code'], $data['type'], $data['price'] ?: 0, $data['image'] ?? null, (int)($data['sort_order'] ?: 0)]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE configurator_colors SET name = ?, hex_code = ?, type = ?, price = ?, image = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['hex_code'], $data['type'], $data['price'] ?: 0, $data['image'] ?? null, (int)($data['sort_order'] ?: 0), $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM configurator_colors WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
