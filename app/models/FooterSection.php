<?php
require_once __DIR__ . '/../Database.php';

class FooterSection {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM footer_sections ORDER BY sort_order ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllWithLinks() {
        $sections = $this->getAll();
        foreach ($sections as &$section) {
            $stmt = $this->db->prepare("SELECT * FROM footer_links WHERE section_id = ? ORDER BY sort_order ASC");
            $stmt->execute([$section['id']]);
            $section['links'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $sections;
    }
    
    public function getSocialLinks() {
        $stmt = $this->db->query("SELECT * FROM social_links ORDER BY sort_order ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO footer_sections (title, sort_order) VALUES (?, ?)");
        return $stmt->execute([$data['title'], $data['sort_order']]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE footer_sections SET title = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$data['title'], $data['sort_order'], $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM footer_sections WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

class FooterLink {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO footer_links (section_id, label, url, sort_order) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['section_id'], $data['label'], $data['url'], $data['sort_order']]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE footer_links SET label = ?, url = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$data['label'], $data['url'], $data['sort_order'], $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM footer_links WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

class SocialLink {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM social_links ORDER BY sort_order ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO social_links (platform, url, icon, sort_order) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['platform'], $data['url'], $data['icon'], $data['sort_order']]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE social_links SET platform = ?, url = ?, icon = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$data['platform'], $data['url'], $data['icon'], $data['sort_order'], $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM social_links WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
