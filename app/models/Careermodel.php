<?php
require_once __DIR__ . '/../Database.php';

class CareerModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── CATEGORIES ────────────────────────────────────────────────────────────
    public function getCategories(): array
    {
        try {
            return $this->db->query(
                "SELECT * FROM career_categories WHERE is_active = TRUE ORDER BY sort_order ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function getAllCategories(): array
    {
        try {
            return $this->db->query(
                "SELECT * FROM career_categories ORDER BY sort_order ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function createCategory(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO career_categories (name, slug, description, icon, color, sort_order, is_active)
             VALUES (:name, :slug, :description, :icon, :color, :sort_order, :is_active)"
        );
        return $stmt->execute([
            'name'        => $data['name'],
            'slug'        => $this->makeUniqueSlug('career_categories', $data['slug'] ?? $data['name']),
            'description' => $data['description'] ?? null,
            'icon'        => $data['icon']        ?? 'fas fa-briefcase',
            'color'       => $data['color']       ?? '#c9a84c',
            'sort_order'  => (int)($data['sort_order'] ?? 0),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
        ]);
    }

    public function updateCategory(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE career_categories SET name=:name, slug=:slug, description=:description,
             icon=:icon, color=:color, sort_order=:sort_order, is_active=:is_active WHERE id=:id"
        );
        return $stmt->execute([
            'id'          => $id,
            'name'        => $data['name'],
            'slug'        => $this->makeUniqueSlug('career_categories', $data['slug'] ?? $data['name'], $id),
            'description' => $data['description'] ?? null,
            'icon'        => $data['icon']        ?? 'fas fa-briefcase',
            'color'       => $data['color']       ?? '#c9a84c',
            'sort_order'  => (int)($data['sort_order'] ?? 0),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
        ]);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->db->prepare("DELETE FROM career_categories WHERE id=?")->execute([$id]);
    }

    // ── JOBS ──────────────────────────────────────────────────────────────────
    public function getActiveJobs(): array
    {
        try {
            return $this->db->query(
                "SELECT j.*, c.name AS category_name, c.slug AS category_slug,
                        c.color AS category_color, c.icon AS category_icon
                 FROM career_jobs j
                 LEFT JOIN career_categories c ON j.category_id = c.id
                 WHERE j.is_active = TRUE
                   AND (j.published_at IS NULL OR j.published_at <= NOW())
                   AND (j.expires_at   IS NULL OR j.expires_at   >  NOW())
                 ORDER BY j.is_featured DESC, j.sort_order ASC, j.created_at DESC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function getAllJobs(): array
    {
        try {
            return $this->db->query(
                "SELECT j.*, c.name AS category_name, c.color AS category_color, c.icon AS category_icon
                 FROM career_jobs j
                 LEFT JOIN career_categories c ON j.category_id = c.id
                 ORDER BY j.sort_order ASC, j.created_at DESC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function getJobById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT j.*, c.name AS category_name, c.color AS category_color
             FROM career_jobs j
             LEFT JOIN career_categories c ON j.category_id = c.id
             WHERE j.id=?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createJob(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO career_jobs
             (category_id, title, short_desc, description, requirements, location,
              employment_type, experience_level, salary_range, apply_url, apply_email,
              deadline, is_active, is_featured, is_urgent, sort_order)
             VALUES
             (:category_id,:title,:short_desc,:description,:requirements,:location,
              :employment_type,:experience_level,:salary_range,:apply_url,:apply_email,
              :deadline,:is_active,:is_featured,:is_urgent,:sort_order)"
        );
        return $stmt->execute([
            'category_id'      => $data['category_id']       ?: null,
            'title'            => $data['title'],
            'short_desc'       => $data['short_desc']         ?? null,
            'description'      => $data['description']        ?? null,
            'requirements'     => $data['requirements']       ?? null,
            'location'         => $data['location']           ?? null,
            'employment_type'  => $data['employment_type']    ?? null,
            'experience_level' => $data['experience_level']   ?? null,
            'salary_range'     => $data['salary_range']       ?? null,
            'apply_url'        => $data['apply_url']          ?? null,
            'apply_email'      => $data['apply_email']        ?? null,
            'deadline'         => $data['deadline']           ?: null,
            'is_active'        => isset($data['is_active'])   ? 1 : 0,
            'is_featured'      => isset($data['is_featured']) ? 1 : 0,
            'is_urgent'        => isset($data['is_urgent'])   ? 1 : 0,
            'sort_order'       => (int)($data['sort_order']   ?? 0),
        ]);
    }

    public function updateJob(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE career_jobs SET
             category_id=:category_id,title=:title,short_desc=:short_desc,
             description=:description,requirements=:requirements,location=:location,
             employment_type=:employment_type,experience_level=:experience_level,
             salary_range=:salary_range,apply_url=:apply_url,apply_email=:apply_email,
             deadline=:deadline,is_active=:is_active,is_featured=:is_featured,
             is_urgent=:is_urgent,sort_order=:sort_order WHERE id=:id"
        );
        return $stmt->execute([
            'id'               => $id,
            'category_id'      => $data['category_id']       ?: null,
            'title'            => $data['title'],
            'short_desc'       => $data['short_desc']         ?? null,
            'description'      => $data['description']        ?? null,
            'requirements'     => $data['requirements']       ?? null,
            'location'         => $data['location']           ?? null,
            'employment_type'  => $data['employment_type']    ?? null,
            'experience_level' => $data['experience_level']   ?? null,
            'salary_range'     => $data['salary_range']       ?? null,
            'apply_url'        => $data['apply_url']          ?? null,
            'apply_email'      => $data['apply_email']        ?? null,
            'deadline'         => $data['deadline']           ?: null,
            'is_active'        => isset($data['is_active'])   ? 1 : 0,
            'is_featured'      => isset($data['is_featured']) ? 1 : 0,
            'is_urgent'        => isset($data['is_urgent'])   ? 1 : 0,
            'sort_order'       => (int)($data['sort_order']   ?? 0),
        ]);
    }

    public function deleteJob(int $id): bool
    {
        $this->db->prepare("DELETE FROM career_job_tags WHERE job_id=?")->execute([$id]);
        return $this->db->prepare("DELETE FROM career_jobs WHERE id=?")->execute([$id]);
    }

    // ── TAGS ──────────────────────────────────────────────────────────────────
    public function getAllTags(): array
    {
        try {
            return $this->db->query(
                "SELECT * FROM career_tags ORDER BY name ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function getJobTagIds(int $jobId): array
    {
        try {
            $stmt = $this->db->prepare("SELECT tag_id FROM career_job_tags WHERE job_id=?");
            $stmt->execute([$jobId]);
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'tag_id');
        } catch (Exception $e) { return []; }
    }

    public function setJobTags(int $jobId, array $tagIds): void
    {
        $this->db->prepare("DELETE FROM career_job_tags WHERE job_id=?")->execute([$jobId]);
        if (!empty($tagIds)) {
            $stmt = $this->db->prepare("INSERT INTO career_job_tags (job_id, tag_id) VALUES (?,?)");
            foreach ($tagIds as $tid) {
                $stmt->execute([$jobId, (int)$tid]);
            }
        }
    }

    public function createTag(string $name): bool
    {
        $stmt = $this->db->prepare("INSERT INTO career_tags (name, slug) VALUES (?,?)");
        return $stmt->execute([$name, $this->makeUniqueSlug('career_tags', $name)]);
    }

    public function deleteTag(int $id): bool
    {
        $this->db->prepare("DELETE FROM career_job_tags WHERE tag_id=?")->execute([$id]);
        return $this->db->prepare("DELETE FROM career_tags WHERE id=?")->execute([$id]);
    }

    public function getJobsWithTags(): array
    {
        $jobs = $this->getAllJobs();
        $allJobTags = [];
        try {
            $rows = $this->db->query(
                "SELECT jt.job_id, t.name, t.slug FROM career_job_tags jt
                 JOIN career_tags t ON jt.tag_id = t.id"
            )->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $allJobTags[$r['job_id']][] = ['name' => $r['name'], 'slug' => $r['slug']];
            }
        } catch (Exception $e) {}
        foreach ($jobs as &$job) {
            $job['tags'] = $allJobTags[$job['id']] ?? [];
        }
        return $jobs;
    }

    // ── ENTRY CARDS ───────────────────────────────────────────────────────────
    public function getEntryCards(string $tabGroup = 'students'): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM career_entry_cards
                 WHERE is_active = TRUE AND tab_group = ?
                 ORDER BY sort_order ASC"
            );
            $stmt->execute([$tabGroup]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function getAllEntryCards(): array
    {
        try {
            return $this->db->query(
                "SELECT * FROM career_entry_cards ORDER BY tab_group ASC, sort_order ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function createEntryCard(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO career_entry_cards (tab_group,title,tag,description,image,link_url,sort_order,is_active)
             VALUES (:tab_group,:title,:tag,:description,:image,:link_url,:sort_order,:is_active)"
        );
        return $stmt->execute([
            'tab_group'   => $data['tab_group']   ?? 'students',
            'title'       => $data['title'],
            'tag'         => $data['tag']          ?? null,
            'description' => $data['description']  ?? null,
            'image'       => $data['image']        ?? null,
            'link_url'    => $data['link_url']     ?? '#',
            'sort_order'  => (int)($data['sort_order'] ?? 0),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
        ]);
    }

    public function updateEntryCard(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE career_entry_cards SET tab_group=:tab_group,title=:title,tag=:tag,
             description=:description,image=:image,link_url=:link_url,
             sort_order=:sort_order,is_active=:is_active WHERE id=:id"
        );
        return $stmt->execute([
            'id'          => $id,
            'tab_group'   => $data['tab_group']   ?? 'students',
            'title'       => $data['title'],
            'tag'         => $data['tag']          ?? null,
            'description' => $data['description']  ?? null,
            'image'       => $data['image']        ?? null,
            'link_url'    => $data['link_url']     ?? '#',
            'sort_order'  => (int)($data['sort_order'] ?? 0),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
        ]);
    }

    public function deleteEntryCard(int $id): bool
    {
        return $this->db->prepare("DELETE FROM career_entry_cards WHERE id=?")->execute([$id]);
    }

    // ── SUBSIDIARIES ──────────────────────────────────────────────────────────
    public function getSubsidiaries(): array
    {
        try {
            return $this->db->query(
                "SELECT * FROM career_subsidiaries WHERE is_active = TRUE ORDER BY sort_order ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function getAllSubsidiaries(): array
    {
        try {
            return $this->db->query(
                "SELECT * FROM career_subsidiaries ORDER BY sort_order ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function createSubsidiary(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO career_subsidiaries (name,slug,description,image,link_url,sort_order,is_active)
             VALUES (:name,:slug,:description,:image,:link_url,:sort_order,:is_active)"
        );
        return $stmt->execute([
            'name'        => $data['name'],
            'slug'        => $this->makeUniqueSlug('career_subsidiaries', $data['name']),
            'description' => $data['description'] ?? null,
            'image'       => $data['image']       ?? null,
            'link_url'    => $data['link_url']    ?? '#',
            'sort_order'  => (int)($data['sort_order'] ?? 0),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
        ]);
    }

    public function updateSubsidiary(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE career_subsidiaries SET name=:name,slug=:slug,description=:description,image=:image,
             link_url=:link_url,sort_order=:sort_order,is_active=:is_active WHERE id=:id"
        );
        return $stmt->execute([
            'id'          => $id,
            'name'        => $data['name'],
            'slug'        => $this->makeUniqueSlug('career_subsidiaries', $data['name'], $id),
            'description' => $data['description'] ?? null,
            'image'       => $data['image']       ?? null,
            'link_url'    => $data['link_url']    ?? '#',
            'sort_order'  => (int)($data['sort_order'] ?? 0),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
        ]);
    }

    public function deleteSubsidiary(int $id): bool
    {
        return $this->db->prepare("DELETE FROM career_subsidiaries WHERE id=?")->execute([$id]);
    }

    // ── CONTENTS ──────────────────────────────────────────────────────────────
    public function getContent(string $key, string $fallback = ''): string
    {
        static $cache = null;
        if ($cache === null) {
            try {
                $rows  = $this->db->query("SELECT key_name, value FROM career_contents")->fetchAll(PDO::FETCH_ASSOC);
                $cache = array_column($rows, 'value', 'key_name');
            } catch (Exception $e) {
                $cache = [];
            }
        }
        return htmlspecialchars($cache[$key] ?? $fallback, ENT_QUOTES);
    }

    public function getRawContent(string $key, string $fallback = ''): string
    {
        static $rawCache = null;
        if ($rawCache === null) {
            try {
                $rows     = $this->db->query("SELECT key_name, value FROM career_contents")->fetchAll(PDO::FETCH_ASSOC);
                $rawCache = array_column($rows, 'value', 'key_name');
            } catch (Exception $e) {
                $rawCache = [];
            }
        }
        return $rawCache[$key] ?? $fallback;
    }

    public function getAllContents(): array
    {
        try {
            return $this->db->query(
                "SELECT * FROM career_contents ORDER BY section ASC, sort_order ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    // ── INI YANG DIFIX: INSERT jika belum ada, UPDATE jika sudah ada ──────────
    public function upsertContent(string $key, string $value): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO career_contents (key_name, value, section, sort_order)
             VALUES (?, ?, 'misc', 0)
             ON CONFLICT (key_name) DO UPDATE SET value = EXCLUDED.value"
        );
        return $stmt->execute([$key, $value]);
    }

    // ── APPLICATIONS ──────────────────────────────────────────────────────────
    public function getApplications(string $status = ''): array
    {
        try {
            if ($status) {
                $stmt = $this->db->prepare(
                    "SELECT a.*, j.title AS job_title
                     FROM career_applications a
                     LEFT JOIN career_jobs j ON a.job_id = j.id
                     WHERE a.status=? ORDER BY a.created_at DESC"
                );
                $stmt->execute([$status]);
            } else {
                $stmt = $this->db->query(
                    "SELECT a.*, j.title AS job_title
                     FROM career_applications a
                     LEFT JOIN career_jobs j ON a.job_id = j.id
                     ORDER BY a.created_at DESC LIMIT 300"
                );
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function countApplicationsByStatus(): array
    {
        try {
            $rows = $this->db->query(
                "SELECT status, COUNT(*) as count FROM career_applications GROUP BY status"
            )->fetchAll(PDO::FETCH_ASSOC);
            return array_column($rows, 'count', 'status');
        } catch (Exception $e) { return []; }
    }

    public function updateApplicationStatus(int $id, string $status, string $notes = ''): bool
    {
        $stmt = $this->db->prepare("UPDATE career_applications SET status=?, notes=? WHERE id=?");
        return $stmt->execute([$status, $notes, $id]);
    }

    public function deleteApplication(int $id): bool
    {
        return $this->db->prepare("DELETE FROM career_applications WHERE id=?")->execute([$id]);
    }

    // ── HELPER ────────────────────────────────────────────────────────────────
    private function makeSlug(string $str): string
    {
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9\s\-]/', '', $str);
        return preg_replace('/[\s\-]+/', '-', $str) ?: 'item';
    }

    private function makeUniqueSlug(string $table, string $name, ?int $excludeId = null): string
    {
        $base = $this->makeSlug($name);
        $slug = $base;
        $i    = 2;

        do {
            $sql    = "SELECT COUNT(*) FROM {$table} WHERE slug = ?" . ($excludeId !== null ? " AND id != ?" : "");
            $params = $excludeId !== null ? [$slug, $excludeId] : [$slug];
            $stmt   = $this->db->prepare($sql);
            $stmt->execute($params);
            $exists = (int)$stmt->fetchColumn();
            if ($exists) $slug = $base . '-' . $i++;
        } while ($exists);

        return $slug;
    }
}