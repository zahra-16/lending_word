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

    /**
     * Update hanya kolom sections (JSON array of section objects).
     * Kompatibel dengan PostgreSQL (JSONB) dan MySQL (JSON / TEXT).
     */
    public function updateSections($id, $sectionsJson) {
        $clean = $this->sanitizeJson($sectionsJson);

        // Coba PostgreSQL JSONB cast dulu; fallback ke plain jika gagal
        try {
            $stmt = $this->db->prepare(
                "UPDATE discover_features SET sections = :s::jsonb WHERE id = :id"
            );
            $stmt->execute(['s' => $clean, 'id' => (int)$id]);
        } catch (\Exception $e) {
            $stmt = $this->db->prepare(
                "UPDATE discover_features SET sections = :s WHERE id = :id"
            );
            $stmt->execute(['s' => $clean, 'id' => (int)$id]);
        }
        return true;
    }

    // ── TAMBAHAN: update konten halaman detail ─────────────────────────────────
    public function updateDetail($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE discover_features SET
                    hero_title        = :hero_title,
                    hero_subtitle     = :hero_subtitle,
                    hero_image        = :hero_image,
                    hero_video_url    = :hero_video_url,
                    sections          = :sections::jsonb,
                    highlights        = :highlights::jsonb,
                    gallery           = :gallery::jsonb,
                    related_models    = :related_models::jsonb,
                    meta_title        = :meta_title,
                    meta_description  = :meta_description,
                    accent_color      = :accent_color,
                    slug              = :slug
                WHERE id = :id
            ");
        } catch (\Exception $e) {
            // MySQL fallback (no ::jsonb cast)
            $stmt = $this->db->prepare("
                UPDATE discover_features SET
                    hero_title        = :hero_title,
                    hero_subtitle     = :hero_subtitle,
                    hero_image        = :hero_image,
                    hero_video_url    = :hero_video_url,
                    sections          = :sections,
                    highlights        = :highlights,
                    gallery           = :gallery,
                    related_models    = :related_models,
                    meta_title        = :meta_title,
                    meta_description  = :meta_description,
                    accent_color      = :accent_color,
                    slug              = :slug
                WHERE id = :id
            ");
        }
        return $stmt->execute([
            'id'               => (int)$id,
            'hero_title'       => $data['hero_title']        ?: null,
            'hero_subtitle'    => $data['hero_subtitle']     ?: null,
            'hero_image'       => $data['hero_image']        ?: null,
            'hero_video_url'   => $data['hero_video_url']    ?: null,
            'sections'         => $this->sanitizeJson($data['sections_json']       ?? '[]'),
            'highlights'       => $this->sanitizeJson($data['highlights_json']     ?? '[]'),
            'gallery'          => $this->sanitizeJson($data['gallery_json']        ?? '[]'),
            'related_models'   => $this->sanitizeJson($data['related_models_json'] ?? '[]'),
            'meta_title'       => $data['meta_title']        ?: null,
            'meta_description' => $data['meta_description']  ?: null,
            'accent_color'     => $data['accent_color']      ?: '#ffffff',
            'slug'             => $this->makeSlug($data['slug'] ?? ''),
        ]);
    }

    private function sanitizeJson($str) {
        $str = trim($str);
        if (empty($str)) return '[]';
        json_decode($str);
        return json_last_error() === JSON_ERROR_NONE ? $str : '[]';
    }

    private function makeSlug($str) {
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9\s\-]/', '', $str);
        $str = preg_replace('/[\s\-]+/', '-', $str);
        return $str ?: null;
    }
}