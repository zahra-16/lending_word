<?php
require_once __DIR__ . '/../Database.php';

class DiscoverGallery {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByFeatureId($featureId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM discover_gallery WHERE feature_id = :fid ORDER BY sort_order ASC, id ASC"
        );
        $stmt->execute(['fid' => (int)$featureId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM discover_gallery WHERE id = :id");
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($featureId, $data) {
        $stmt = $this->db->prepare("
            INSERT INTO discover_gallery
                (feature_id, eyebrow, title, body, tab_id, tab_label,
                 image_top, image_right, image_bottom, sort_order)
            VALUES
                (:feature_id, :eyebrow, :title, :body, :tab_id, :tab_label,
                 :image_top, :image_right, :image_bottom, :sort_order)
        ");
        return $stmt->execute([
            'feature_id'  => (int)$featureId,
            'eyebrow'     => $data['eyebrow']     ?? null,
            'title'       => $data['title']       ?? null,
            'body'        => $data['body']         ?? null,
            'tab_id'      => $data['tab_id']      ?? null,
            'tab_label'   => $data['tab_label']   ?? null,
            'image_top'   => $data['image_top']   ?? null,
            'image_right' => $data['image_right'] ?? null,
            'image_bottom'=> $data['image_bottom']?? null,
            'sort_order'  => (int)($data['sort_order'] ?? 0),
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE discover_gallery SET
                eyebrow      = :eyebrow,
                title        = :title,
                body         = :body,
                tab_id       = :tab_id,
                tab_label    = :tab_label,
                image_top    = :image_top,
                image_right  = :image_right,
                image_bottom = :image_bottom,
                sort_order   = :sort_order
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'          => (int)$id,
            'eyebrow'     => $data['eyebrow']     ?? null,
            'title'       => $data['title']       ?? null,
            'body'        => $data['body']         ?? null,
            'tab_id'      => $data['tab_id']      ?? null,
            'tab_label'   => $data['tab_label']   ?? null,
            'image_top'   => $data['image_top']   ?? null,
            'image_right' => $data['image_right'] ?? null,
            'image_bottom'=> $data['image_bottom']?? null,
            'sort_order'  => (int)($data['sort_order'] ?? 0),
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM discover_gallery WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
    }

    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }
}