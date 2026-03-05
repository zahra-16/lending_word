<?php
require_once __DIR__ . '/../Database.php';

class ExploreModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM explore_models ORDER BY sort_order ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM explore_models WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE explore_models 
            SET name = :name, description = :description, fuel_types = :fuel_types, 
                doors = :doors, seats = :seats, image = :image, sort_order = :sort_order
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'],
            'fuel_types' => $data['fuel_types'],
            'doors' => $data['doors'],
            'seats' => $data['seats'],
            'image' => $data['image'],
            'sort_order' => $data['sort_order']
        ]);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO explore_models (name, description, fuel_types, doors, seats, image, sort_order)
            VALUES (:name, :description, :fuel_types, :doors, :seats, :image, :sort_order)
        ");
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'fuel_types' => $data['fuel_types'],
            'doors' => $data['doors'],
            'seats' => $data['seats'],
            'image' => $data['image'],
            'sort_order' => $data['sort_order']
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM explore_models WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
