<?php
require_once __DIR__ . '/../Database.php';

class ModelGallery {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getByVariantId($variantId) {
        $stmt = $this->db->prepare("SELECT * FROM model_gallery WHERE variant_id = ? ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$variantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($variantId, $imageUrl, $title = '', $section = '', $caption = '', $sortOrder = 0) {
        $stmt = $this->db->prepare("INSERT INTO model_gallery (variant_id, image_url, title, section, caption, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$variantId, $imageUrl, $title, $section, $caption, $sortOrder]);
    }
    
    public function update($id, $imageUrl, $title = '', $section = '', $caption = '', $sortOrder = 0) {
        $stmt = $this->db->prepare("UPDATE model_gallery SET image_url = ?, title = ?, section = ?, caption = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$imageUrl, $title, $section, $caption, $sortOrder, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM model_gallery WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function deleteByVariantId($variantId) {
        $stmt = $this->db->prepare("DELETE FROM model_gallery WHERE variant_id = ?");
        return $stmt->execute([$variantId]);
    }
}
