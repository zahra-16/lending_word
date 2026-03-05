<?php
require_once __DIR__ . '/../Database.php';

class Model {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM models ORDER BY sort_order ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM models WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($name, $fuel_types, $image, $sort_order) {
        $stmt = $this->db->prepare("INSERT INTO models (name, fuel_types, image, sort_order) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $fuel_types, $image, $sort_order]);
    }
    
    public function update($id, $name, $fuel_types, $image, $sort_order) {
        $stmt = $this->db->prepare("UPDATE models SET name = ?, fuel_types = ?, image = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$name, $fuel_types, $image, $sort_order, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM models WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
