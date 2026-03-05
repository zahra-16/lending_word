<?php
require_once __DIR__ . '/../Database.php';

class BodyType {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM body_types ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM body_types WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO body_types (name, slug) VALUES (?, ?)");
        return $stmt->execute([$data['name'], $data['slug']]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE body_types SET name = ?, slug = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['slug'], $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM body_types WHERE id = ?");
        return $stmt->execute([$id]);
    }
}