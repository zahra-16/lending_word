<?php
require_once __DIR__ . '/../Database.php';

class ModelSeries {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM model_series WHERE is_active = true ORDER BY sort_order ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM model_series WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO model_series (name, slug, description, image_url, sort_order) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'] ?? null,
            $data['image_url'] ?? null,
            $data['sort_order'] ?? 0
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE model_series SET name = ?, slug = ?, description = ?, image_url = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'] ?? null,
            $data['image_url'] ?? null,
            $data['sort_order'] ?? 0,
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM model_series WHERE id = ?");
        return $stmt->execute([$id]);
    }
}