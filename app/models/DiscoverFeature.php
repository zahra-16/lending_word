<?php
require_once __DIR__ . '/../Database.php';

class DiscoverFeature {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM discover_features ORDER BY sort_order ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM discover_features WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO discover_features 
                (title, description, image, category, stats, link_url, link_label, is_featured, sort_order)
            VALUES 
                (:title, :description, :image, :category, :stats, :link_url, :link_label, :is_featured, :sort_order)
        ");
        return $stmt->execute([
            'title'       => $data['title'],
            'description' => $data['description'] ?? '',
            'image'       => $data['image'],
            'category'    => $data['category']    ?? '',
            'stats'       => $data['stats']        ?? null,
            'link_url'    => $data['link_url']     ?? '',
            'link_label'  => $data['link_label']   ?? 'Pelajari Lebih Lanjut',
            'is_featured' => isset($data['is_featured']) ? 1 : 0,
            'sort_order'  => (int)($data['sort_order'] ?? 0),
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE discover_features SET
                title       = :title,
                description = :description,
                image       = :image,
                category    = :category,
                stats       = :stats,
                link_url    = :link_url,
                link_label  = :link_label,
                is_featured = :is_featured,
                sort_order  = :sort_order
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'          => (int)$id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? '',
            'image'       => $data['image'],
            'category'    => $data['category']    ?? '',
            'stats'       => $data['stats']        ?? null,
            'link_url'    => $data['link_url']     ?? '',
            'link_label'  => $data['link_label']   ?? 'Pelajari Lebih Lanjut',
            'is_featured' => isset($data['is_featured']) ? 1 : 0,
            'sort_order'  => (int)($data['sort_order'] ?? 0),
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM discover_features WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
    }
}