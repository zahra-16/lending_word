<?php
require_once __DIR__ . '/../Database.php';

class ModelSpecificationSection {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getByVariantId($variantId) {
        $stmt = $this->db->prepare("SELECT * FROM model_specification_sections WHERE variant_id = ? ORDER BY sort_order ASC");
        $stmt->execute([$variantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM model_specification_sections WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getSectionImages($sectionId) {
        $stmt = $this->db->prepare("SELECT * FROM model_specification_carousel_images WHERE section_id = ? ORDER BY sort_order ASC");
        $stmt->execute([$sectionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getHeroCards($sectionId) {
        $stmt = $this->db->prepare("SELECT * FROM model_specification_hero_cards WHERE section_id = ? ORDER BY sort_order ASC");
        $stmt->execute([$sectionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($variantId, $backgroundImage, $title, $description, $sortOrder = 0) {
        $stmt = $this->db->prepare("INSERT INTO model_specification_sections (variant_id, background_image, title, description, sort_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$variantId, $backgroundImage, $title, $description, $sortOrder]);
        return $this->db->lastInsertId();
    }
    
    public function update($id, $backgroundImage, $title, $description, $sortOrder = 0) {
        $stmt = $this->db->prepare("UPDATE model_specification_sections SET background_image = ?, title = ?, description = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$backgroundImage, $title, $description, $sortOrder, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM model_specification_sections WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function addImage($sectionId, $imageUrl, $title, $description, $sortOrder = 0) {
        $stmt = $this->db->prepare("INSERT INTO model_specification_carousel_images (section_id, image_url, title, description, sort_order) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$sectionId, $imageUrl, $title, $description, $sortOrder]);
    }
    
    public function updateImage($id, $imageUrl, $title, $description, $sortOrder = 0) {
        $stmt = $this->db->prepare("UPDATE model_specification_carousel_images SET image_url = ?, title = ?, description = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$imageUrl, $title, $description, $sortOrder, $id]);
    }
    
    public function deleteImage($id) {
        $stmt = $this->db->prepare("DELETE FROM model_specification_carousel_images WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getImageById($id) {
        $stmt = $this->db->prepare("SELECT * FROM model_specification_carousel_images WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Hero Cards Methods
    public function addHeroCard($sectionId, $imageUrl, $title, $description, $sortOrder = 0) {
        $stmt = $this->db->prepare("INSERT INTO model_specification_hero_cards (section_id, image_url, title, description, sort_order) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$sectionId, $imageUrl, $title, $description, $sortOrder]);
    }
    
    public function updateHeroCard($id, $imageUrl, $title, $description, $sortOrder = 0) {
        $stmt = $this->db->prepare("UPDATE model_specification_hero_cards SET image_url = ?, title = ?, description = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$imageUrl, $title, $description, $sortOrder, $id]);
    }
    
    public function deleteHeroCard($id) {
        $stmt = $this->db->prepare("DELETE FROM model_specification_hero_cards WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getHeroCardById($id) {
        $stmt = $this->db->prepare("SELECT * FROM model_specification_hero_cards WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
