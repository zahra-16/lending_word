<?php
require_once __DIR__ . '/../Database.php';

class Content {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Get single content
    public function get($section, $key) {
        $stmt = $this->db->prepare("SELECT value FROM content WHERE section = ? AND key_name = ?");
        $stmt->execute([$section, $key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['value'] : '';
    }
    
    // Get all content
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM content ORDER BY section, key_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get content grouped by section
    public function getAllGrouped() {
        $contents = $this->getAll();
        $grouped = [];
        foreach ($contents as $content) {
            $grouped[$content['section']][] = $content;
        }
        return $grouped;
    }
    
    // Update content
    public function update($id, $value) {
        $stmt = $this->db->prepare("UPDATE content SET value = ? WHERE id = ?");
        return $stmt->execute([$value, $id]);
    }
    
    // Bulk update
    public function bulkUpdate($data) {
        foreach ($data as $id => $value) {
            $this->update($id, $value);
        }
        return true;
    }
}
