<?php
require_once __DIR__ . '/../Database.php';

class ConfiguratorWheel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM configurator_wheels ORDER BY sort_order ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM configurator_wheels WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO configurator_wheels (name, size, price, image, sort_order) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['name'], $data['size'], $data['price'] ?: 0, $data['image'] ?? null, (int)($data['sort_order'] ?: 0)]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE configurator_wheels SET name = ?, size = ?, price = ?, image = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['size'], $data['price'] ?: 0, $data['image'] ?? null, (int)($data['sort_order'] ?: 0), $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM configurator_wheels WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
