<?php
require_once __DIR__ . '/../Database.php';

class NavbarLink {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM navbar_links ORDER BY sort_order ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO navbar_links (label, url, sort_order) VALUES (?, ?, ?)");
        return $stmt->execute([$data['label'], $data['url'], $data['sort_order']]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE navbar_links SET label = ?, url = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$data['label'], $data['url'], $data['sort_order'], $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM navbar_links WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
