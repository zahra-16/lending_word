<?php
/**
 * GpcModel.php
 * Letakkan di: /lending_word/app/models/GpcModel.php
 */
require_once __DIR__ . '/../Database.php';

class GpcModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── CONTENTS ──────────────────────────────────────────────────────────────

    public function getContent(string $key, string $fallback = ''): string
    {
        static $cache = null;
        if ($cache === null) {
            try {
                $rows  = $this->db->query("SELECT key_name, value FROM gpc_contents")->fetchAll(PDO::FETCH_ASSOC);
                $cache = array_column($rows, 'value', 'key_name');
            } catch (Exception $e) { $cache = []; }
        }
        return htmlspecialchars($cache[$key] ?? $fallback, ENT_QUOTES);
    }

    public function getRawContent(string $key, string $fallback = ''): string
    {
        static $rawCache = null;
        if ($rawCache === null) {
            try {
                $rows     = $this->db->query("SELECT key_name, value FROM gpc_contents")->fetchAll(PDO::FETCH_ASSOC);
                $rawCache = array_column($rows, 'value', 'key_name');
            } catch (Exception $e) { $rawCache = []; }
        }
        return $rawCache[$key] ?? $fallback;
    }

    public function getAllContents(): array
    {
        try {
            return $this->db->query(
                "SELECT * FROM gpc_contents ORDER BY section ASC, sort_order ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function upsertContent(string $key, string $value): bool
    {
        $stmt = $this->db->prepare("UPDATE gpc_contents SET value=?, updated_at=NOW() WHERE key_name=?");
        $stmt->execute([$value, $key]);
        if ($stmt->rowCount() === 0) {
            try {
                $ins = $this->db->prepare(
                    "INSERT INTO gpc_contents (key_name, value, section, label, type, sort_order)
                     VALUES (?, ?, 'misc', ?, 'text', 99)"
                );
                $ins->execute([$key, $value, ucwords(str_replace('_', ' ', $key))]);
            } catch (Exception $e) {}
        }
        return true;
    }

    // ── PARTNERS ──────────────────────────────────────────────────────────────

    public function getActivePartners(): array
    {
        try {
            return $this->db->query(
                "SELECT * FROM gpc_partners WHERE is_active = TRUE ORDER BY sort_order ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function getAllPartners(): array
    {
        try {
            return $this->db->query(
                "SELECT * FROM gpc_partners ORDER BY sort_order ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function createPartner(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO gpc_partners (name, logo_url, link_url, description, sort_order, is_active)
             VALUES (:name,:logo_url,:link_url,:description,:sort_order,:is_active)"
        );
        return $stmt->execute([
            'name'        => $data['name'],
            'logo_url'    => $data['logo_url']    ?? null,
            'link_url'    => $data['link_url']    ?? '#',
            'description' => $data['description'] ?? null,
            'sort_order'  => (int)($data['sort_order'] ?? 0),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
        ]);
    }

    public function updatePartner(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE gpc_partners SET name=:name,logo_url=:logo_url,link_url=:link_url,
             description=:description,sort_order=:sort_order,is_active=:is_active WHERE id=:id"
        );
        return $stmt->execute([
            'id'          => $id,
            'name'        => $data['name'],
            'logo_url'    => $data['logo_url']    ?? null,
            'link_url'    => $data['link_url']    ?? '#',
            'description' => $data['description'] ?? null,
            'sort_order'  => (int)($data['sort_order'] ?? 0),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
        ]);
    }

    public function deletePartner(int $id): bool
    {
        return $this->db->prepare("DELETE FROM gpc_partners WHERE id=?")->execute([$id]);
    }

    // ── COOPERATIONS ──────────────────────────────────────────────────────────

    public function getActiveCooperations(): array
    {
        try {
            return $this->db->query(
                "SELECT * FROM gpc_cooperations WHERE is_active = TRUE ORDER BY sort_order ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function getAllCooperations(): array
    {
        try {
            return $this->db->query(
                "SELECT * FROM gpc_cooperations ORDER BY sort_order ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function createCooperation(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO gpc_cooperations (title, description, image_url, link_url, sort_order, is_active)
             VALUES (:title,:description,:image_url,:link_url,:sort_order,:is_active)"
        );
        return $stmt->execute([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'image_url'   => $data['image_url']   ?? null,
            'link_url'    => $data['link_url']     ?? '#',
            'sort_order'  => (int)($data['sort_order'] ?? 0),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
        ]);
    }

    public function updateCooperation(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE gpc_cooperations SET title=:title,description=:description,image_url=:image_url,
             link_url=:link_url,sort_order=:sort_order,is_active=:is_active WHERE id=:id"
        );
        return $stmt->execute([
            'id'          => $id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'image_url'   => $data['image_url']   ?? null,
            'link_url'    => $data['link_url']     ?? '#',
            'sort_order'  => (int)($data['sort_order'] ?? 0),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
        ]);
    }

    public function deleteCooperation(int $id): bool
    {
        return $this->db->prepare("DELETE FROM gpc_cooperations WHERE id=?")->execute([$id]);
    }
}