<?php
require_once __DIR__ . '/../Database.php';

class ModelVariant {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getCategories() {
        // Get total count
        $totalStmt = $this->db->query("SELECT COUNT(*) as total FROM model_variants");
        $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Try to get categories with counts
        $categories = [];
        try {
            $stmt = $this->db->query("
                SELECT mc.*, 
                       COALESCE(COUNT(mv.id), 0) as count
                FROM model_categories mc
                LEFT JOIN model_variants mv ON mc.id = mv.category_id
                WHERE mc.slug != 'all'
                GROUP BY mc.id, mc.name, mc.slug, mc.sort_order
                ORDER BY mc.sort_order ASC
            ");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If model_categories doesn't exist, create default categories based on variant names
            $stmt = $this->db->query("
                SELECT DISTINCT 
                    CASE 
                        WHEN name LIKE '%718%' THEN '718'
                        WHEN name LIKE '%911%' THEN '911'
                        WHEN name LIKE '%Taycan%' THEN 'Taycan'
                        WHEN name LIKE '%Macan%' THEN 'Macan'
                        WHEN name LIKE '%Cayenne%' THEN 'Cayenne'
                        WHEN name LIKE '%Panamera%' THEN 'Panamera'
                        ELSE 'Other'
                    END as category,
                    COUNT(*) as count
                FROM model_variants
                GROUP BY category
                ORDER BY category
            ");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $idx => $result) {
                $categories[] = [
                    'id' => $idx + 1,
                    'name' => $result['category'],
                    'slug' => strtolower($result['category']),
                    'sort_order' => $idx + 1,
                    'count' => $result['count']
                ];
            }
        }
        
        // Add 'All' category at the beginning
        array_unshift($categories, [
            'id' => 0,
            'name' => 'All',
            'slug' => 'all',
            'sort_order' => 0,
            'count' => $total
        ]);
        
        return $categories;
    }
    
    public function getVariantsByCategory($categorySlug = 'all') {
        // Determine ORDER BY based on available columns
        $orderBy = "ORDER BY id ASC";
        try {
            $stmt = $this->db->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name='model_variants' AND column_name='sort_order'
            ");
            if ($stmt->rowCount() > 0) {
                $orderBy = "ORDER BY sort_order ASC, id ASC";
            }
        } catch (PDOException $e) {
            // Keep default ORDER BY
        }
        
        if ($categorySlug === 'all') {
            $stmt = $this->db->query("SELECT * FROM model_variants $orderBy");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Try with category_id first
            try {
                $stmt = $this->db->prepare("
                    SELECT mv.* FROM model_variants mv
                    JOIN model_categories mc ON mv.category_id = mc.id
                    WHERE LOWER(mc.slug) = LOWER(?)
                    $orderBy
                ");
                $stmt->execute([$categorySlug]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Fall back to name-based filtering
                $pattern = '%' . str_replace('-', ' ', $categorySlug) . '%';
                $stmt = $this->db->prepare("
                    SELECT * FROM model_variants 
                    WHERE LOWER(name) LIKE LOWER(?)
                    $orderBy
                ");
                $stmt->execute([$pattern]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    }
    
    public function getVariantsGrouped($categorySlug = 'all') {
        $variants = $this->getVariantsByCategory($categorySlug);
        $grouped = [];
        
        foreach ($variants as $variant) {
            $group = $variant['variant_group'] ?? 'Other';
            if (empty($group)) {
                $group = 'Other';
            }
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $variant;
        }
        
        return $grouped;
    }
    
    public function getFilters() {
        $filters = [
            'body_design' => [],
            'seats' => [],
            'drive_type' => [],
            'fuel_type' => []
        ];
        
        // Get filters safely
        $columns = ['body_design', 'seats', 'drive_type', 'fuel_type'];
        foreach ($columns as $column) {
            try {
                $stmt = $this->db->query("
                    SELECT DISTINCT $column 
                    FROM model_variants 
                    WHERE $column IS NOT NULL 
                    ORDER BY $column
                ");
                $filters[$column] = $stmt->fetchAll(PDO::FETCH_COLUMN);
            } catch (PDOException $e) {
                $filters[$column] = [];
            }
        }
        
        return $filters;
    }
    
    public function create($data) {
        // Check which columns exist
        $availableColumns = [];
        try {
            $stmt = $this->db->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'model_variants'
            ");
            $availableColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            // Use basic columns
            $availableColumns = ['name', 'image'];
        }
        
        // Build insert based on available columns
        $columns = [];
        $values = [];
        $params = [];
        
        // Always include name and image
        if (in_array('name', $availableColumns)) {
            $columns[] = 'name';
            $params[] = '?';
            $values[] = $data['name'];
        }
        
        if (in_array('image', $availableColumns)) {
            $columns[] = 'image';
            $params[] = '?';
            $values[] = $data['image'];
        }
        
        // Add optional columns if they exist
        $optionalFields = [
            'category_id' => $data['category_id'] ?? null,
            'variant_group' => $data['variant_group'] ?? null,
            'fuel_type' => $data['fuel_type'] ?? null,
            'drive_type' => $data['drive_type'] ?? null,
            'transmission' => $data['transmission'] ?? null,
            'acceleration' => $data['acceleration'] ?? null,
            'power_kw' => is_numeric($data['power_kw'] ?? '') ? $data['power_kw'] : null,
            'power_ps' => is_numeric($data['power_ps'] ?? '') ? $data['power_ps'] : null,
            'top_speed' => $data['top_speed'] ?? null,
            'body_design' => $data['body_design'] ?? null,
            'seats' => is_numeric($data['seats'] ?? '') ? $data['seats'] : null,
            'is_new' => isset($data['is_new']) ? 1 : 0,
            'sort_order' => is_numeric($data['sort_order'] ?? '') ? (int)$data['sort_order'] : 0,
            'configurator_url' => $data['configurator_url'] ?? null,
            'hero_bg_image' => $data['hero_bg_image'] ?? null,
            'model_video' => $data['model_video'] ?? null,
            'model_audio' => $data['model_audio'] ?? null
        ];
        
        foreach ($optionalFields as $column => $value) {
            if (in_array($column, $availableColumns)) {
                $columns[] = $column;
                $params[] = '?';
                $values[] = $value;
            }
        }
        
        if (empty($columns)) {
            return false;
        }
        
        $columnsStr = implode(', ', $columns);
        $paramsStr = implode(', ', $params);
        
        $stmt = $this->db->prepare("
            INSERT INTO model_variants ($columnsStr)
            VALUES ($paramsStr)
        ");
        return $stmt->execute($values);
    }
    
    public function update($id, $data) {
        // Check which columns exist
        $availableColumns = [];
        try {
            $stmt = $this->db->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'model_variants'
            ");
            $availableColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            // Use basic columns
            $availableColumns = ['name', 'image'];
        }
        
        // Build update based on available columns
        $updates = [];
        $values = [];
        
        // Always update name and image
        if (in_array('name', $availableColumns)) {
            $updates[] = 'name = ?';
            $values[] = $data['name'];
        }
        
        if (in_array('image', $availableColumns)) {
            $updates[] = 'image = ?';
            $values[] = $data['image'];
        }
        
        // Add optional columns if they exist
        $optionalFields = [
            'category_id' => $data['category_id'] ?? null,
            'variant_group' => ($data['variant_group'] ?? '') ?: null,
            'fuel_type' => ($data['fuel_type'] ?? '') ?: null,
            'drive_type' => ($data['drive_type'] ?? '') ?: null,
            'transmission' => ($data['transmission'] ?? '') ?: null,
            'acceleration' => ($data['acceleration'] ?? '') ?: null,
            'power_kw' => is_numeric($data['power_kw'] ?? '') ? $data['power_kw'] : null,
            'power_ps' => is_numeric($data['power_ps'] ?? '') ? $data['power_ps'] : null,
            'top_speed' => ($data['top_speed'] ?? '') ?: null,
            'body_design' => ($data['body_design'] ?? '') ?: null,
            'seats' => is_numeric($data['seats'] ?? '') ? $data['seats'] : null,
            'is_new' => isset($data['is_new']) ? 1 : 0,
            'sort_order' => is_numeric($data['sort_order'] ?? '') ? (int)$data['sort_order'] : 0,
            'configurator_url' => ($data['configurator_url'] ?? '') ?: null,
            'hero_bg_image' => ($data['hero_bg_image'] ?? '') ?: null,
            'model_video' => ($data['model_video'] ?? '') ?: null,
            'model_audio' => ($data['model_audio'] ?? '') ?: null
        ];
        
        foreach ($optionalFields as $column => $value) {
            if (in_array($column, $availableColumns)) {
                $updates[] = "$column = ?";
                $values[] = $value;
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $values[] = $id; // Add ID at the end
        
        $updatesStr = implode(', ', $updates);
        
        $stmt = $this->db->prepare("
            UPDATE model_variants 
            SET $updatesStr
            WHERE id = ?
        ");
        return $stmt->execute($values);
    }
    
    public function getById($id) {
        // Try with category join first
        try {
            $stmt = $this->db->prepare("
                SELECT mv.*, mc.name as category_name, mc.slug as category_slug 
                FROM model_variants mv
                LEFT JOIN model_categories mc ON mv.category_id = mc.id
                WHERE mv.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fall back to simple query
            $stmt = $this->db->prepare("SELECT * FROM model_variants WHERE id = ?");
            $stmt->execute([$id]);
            $variant = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Add empty category fields
            if ($variant) {
                $variant['category_name'] = null;
                $variant['category_slug'] = null;
            }
            
            return $variant;
        }
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM model_variants WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getAll() {
        // Determine ORDER BY based on available columns
        $orderBy = "ORDER BY id ASC";
        try {
            $stmt = $this->db->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name='model_variants' AND column_name='sort_order'
            ");
            if ($stmt->rowCount() > 0) {
                $orderBy = "ORDER BY sort_order ASC, id ASC";
            }
        } catch (PDOException $e) {
            // Keep default ORDER BY
        }
        
        $stmt = $this->db->query("SELECT * FROM model_variants $orderBy");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}