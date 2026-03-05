<?php
require_once __DIR__ . '/../Database.php';

class Vehicle {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM v_vehicles_complete ORDER BY is_featured DESC, sort_order ASC, created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM v_vehicles_complete WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function search($filters) {
        $sql = "SELECT DISTINCT v.* FROM v_vehicles_complete v WHERE 1=1";
        $params = [];
        
        if (!empty($filters['condition'])) {
            $sql .= " AND v.condition = ?";
            $params[] = $filters['condition'];
        }
        
        if (!empty($filters['series_id'])) {
            $sql .= " AND v.series_id = ?";
            $params[] = $filters['series_id'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND v.price >= ?";
            $params[] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND v.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['fuel_type'])) {
            if (is_array($filters['fuel_type'])) {
                $placeholders = str_repeat('?,', count($filters['fuel_type']) - 1) . '?';
                $sql .= " AND v.fuel_type IN ($placeholders)";
                $params = array_merge($params, $filters['fuel_type']);
            } else {
                $sql .= " AND v.fuel_type = ?";
                $params[] = $filters['fuel_type'];
            }
        }
        
        if (!empty($filters['transmission'])) {
            if (is_array($filters['transmission'])) {
                $conditions = [];
                foreach ($filters['transmission'] as $trans) {
                    $conditions[] = "v.transmission LIKE ?";
                    $params[] = '%' . $trans . '%';
                }
                $sql .= " AND (" . implode(' OR ', $conditions) . ")";
            } else {
                $sql .= " AND v.transmission LIKE ?";
                $params[] = '%' . $filters['transmission'] . '%';
            }
        }
        
        if (!empty($filters['body_type_id'])) {
            if (is_array($filters['body_type_id'])) {
                $placeholders = str_repeat('?,', count($filters['body_type_id']) - 1) . '?';
                $sql .= " AND v.body_type_id IN ($placeholders)";
                $params = array_merge($params, $filters['body_type_id']);
            } else {
                $sql .= " AND v.body_type_id = ?";
                $params[] = $filters['body_type_id'];
            }
        }
        
        if (!empty($filters['min_year'])) {
            $sql .= " AND v.model_year >= ?";
            $params[] = $filters['min_year'];
        }
        if (!empty($filters['max_year'])) {
            $sql .= " AND v.model_year <= ?";
            $params[] = $filters['max_year'];
        }
        
        if (!empty($filters['color_category'])) {
            if (is_array($filters['color_category'])) {
                $placeholders = str_repeat('?,', count($filters['color_category']) - 1) . '?';
                $sql .= " AND v.exterior_color_category IN ($placeholders)";
                $params = array_merge($params, $filters['color_category']);
            } else {
                $sql .= " AND v.exterior_color_category = ?";
                $params[] = $filters['color_category'];
            }
        }
        
        if (!empty($filters['center_id'])) {
            $sql .= " AND v.center_id = ?";
            $params[] = $filters['center_id'];
        }
        
        $sortBy = $filters['sort_by'] ?? 'recommended';
        switch ($sortBy) {
            case 'price_asc':  $sql .= " ORDER BY v.price ASC"; break;
            case 'price_desc': $sql .= " ORDER BY v.price DESC"; break;
            case 'year_desc':  $sql .= " ORDER BY v.model_year DESC, v.created_at DESC"; break;
            case 'year_asc':   $sql .= " ORDER BY v.model_year ASC, v.created_at DESC"; break;
            case 'mileage_asc':$sql .= " ORDER BY v.mileage ASC NULLS LAST"; break;
            default:           $sql .= " ORDER BY v.is_featured DESC, v.sort_order ASC, v.created_at DESC";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getFilterOptions() {
        return [
            'fuel_types'    => $this->getDistinctValues('fuel_type'),
            'transmissions' => $this->getDistinctTransmissions(),
            'years'         => $this->getYearRange(),
            'colors'        => $this->getDistinctValues('exterior_color_category'),
            'price_range'   => $this->getPriceRange()
        ];
    }
    
    private function getDistinctValues($column) {
        $stmt = $this->db->query("SELECT DISTINCT $column FROM vehicles WHERE $column IS NOT NULL AND is_active = true ORDER BY $column");
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), $column);
    }
    
    private function getDistinctTransmissions() {
        $stmt = $this->db->query("SELECT DISTINCT transmission FROM vehicles WHERE transmission IS NOT NULL AND is_active = true ORDER BY transmission");
        $transmissions = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'transmission');
        $simplified = [];
        foreach ($transmissions as $trans) {
            if (stripos($trans, 'PDK') !== false || stripos($trans, 'Automatic') !== false) {
                $simplified['Automatic'] = 'Automatic';
            } elseif (stripos($trans, 'Manual') !== false) {
                $simplified['Manual'] = 'Manual';
            }
        }
        return array_values($simplified);
    }
    
    private function getYearRange() {
        $stmt = $this->db->query("SELECT MIN(model_year) as min_year, MAX(model_year) as max_year FROM vehicles WHERE is_active = true");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function getPriceRange() {
        $stmt = $this->db->query("SELECT MIN(price) as min_price, MAX(price) as max_price FROM vehicles WHERE is_active = true");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getImages($vehicleId) {
        $stmt = $this->db->prepare("SELECT * FROM vehicle_images WHERE vehicle_id = ? ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$vehicleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSimilar($vehicleId, $limit = 4) {
        $vehicle = $this->getById($vehicleId);
        if (!$vehicle) return [];
        $stmt = $this->db->prepare("
            SELECT * FROM v_vehicles_complete 
            WHERE id != ? 
            AND (series_id = ? OR body_type_id = ?)
            AND is_active = true
            ORDER BY 
                CASE WHEN series_id = ? THEN 1 ELSE 2 END,
                is_featured DESC,
                created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$vehicleId, $vehicle['series_id'], $vehicle['body_type_id'], $vehicle['series_id'], $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function incrementViewCount($id) {
        $stmt = $this->db->prepare("UPDATE vehicles SET view_count = view_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    private function emptyToNull($value) {
        return ($value === '' || $value === null) ? null : $value;
    }
    
    private function toBool($value) {
        if ($value === null || $value === '' || $value === false || 
            $value === 0 || $value === '0' || $value === 'false' || 
            $value === 'off' || $value === 'no') {
            return false;
        }
        return (bool)$value;
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO vehicles (
                center_id, series_id, variant_id, body_type_id,
                title, condition, is_approved_preowned,
                model_year, fuel_type, power_kw, power_hp,
                transmission, drive_type,
                exterior_color, exterior_color_category, exterior_color_hex,
                interior_color, interior_color_hex, interior_material, seats,
                price, main_image_url,
                mileage, stock_number, vin,
                acceleration_0_100, top_speed,
                is_featured, is_active
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bindValue(1,  $this->emptyToNull($data['center_id'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(2,  $this->emptyToNull($data['series_id'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(3,  $this->emptyToNull($data['variant_id'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(4,  $this->emptyToNull($data['body_type_id'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(5,  $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(6,  $data['condition'], PDO::PARAM_STR);
        $stmt->bindValue(7,  $this->toBool($data['is_approved_preowned'] ?? 0), PDO::PARAM_BOOL);
        $stmt->bindValue(8,  $data['model_year'], PDO::PARAM_INT);
        $stmt->bindValue(9,  $this->emptyToNull($data['fuel_type'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(10, $this->emptyToNull($data['power_kw'] ?? null), is_null($this->emptyToNull($data['power_kw'] ?? null)) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(11, $this->emptyToNull($data['power_hp'] ?? null), is_null($this->emptyToNull($data['power_hp'] ?? null)) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(12, $this->emptyToNull($data['transmission'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(13, $this->emptyToNull($data['drive_type'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(14, $this->emptyToNull($data['exterior_color'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(15, $this->emptyToNull($data['exterior_color_category'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(16, $this->emptyToNull($data['exterior_color_hex'] ?? null), PDO::PARAM_STR); // ← BARU
        $stmt->bindValue(17, $this->emptyToNull($data['interior_color'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(18, $this->emptyToNull($data['interior_color_hex'] ?? null), PDO::PARAM_STR); // ← BARU
        $stmt->bindValue(19, $this->emptyToNull($data['interior_material'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(20, $this->emptyToNull($data['seats'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(21, $data['price'], PDO::PARAM_INT);
        $stmt->bindValue(22, $data['main_image_url'], PDO::PARAM_STR);
        $stmt->bindValue(23, $this->emptyToNull($data['mileage'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(24, $this->emptyToNull($data['stock_number'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(25, $this->emptyToNull($data['vin'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(26, $this->emptyToNull($data['acceleration_0_100'] ?? null), is_null($this->emptyToNull($data['acceleration_0_100'] ?? null)) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(27, $this->emptyToNull($data['top_speed'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(28, $this->toBool($data['is_featured'] ?? 0), PDO::PARAM_BOOL);
        $stmt->bindValue(29, $this->toBool($data['is_active'] ?? 1), PDO::PARAM_BOOL);
        
        $result = $stmt->execute();
        return $result ? $this->db->lastInsertId() : false;
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE vehicles SET
                center_id = ?,
                series_id = ?,
                variant_id = ?,
                body_type_id = ?,
                title = ?,
                condition = ?,
                is_approved_preowned = ?,
                model_year = ?,
                fuel_type = ?,
                power_kw = ?,
                power_hp = ?,
                transmission = ?,
                drive_type = ?,
                exterior_color = ?,
                exterior_color_category = ?,
                exterior_color_hex = ?,
                interior_color = ?,
                interior_color_hex = ?,
                interior_material = ?,
                seats = ?,
                price = ?,
                main_image_url = ?,
                mileage = ?,
                stock_number = ?,
                vin = ?,
                acceleration_0_100 = ?,
                top_speed = ?,
                is_featured = ?,
                is_active = ?
            WHERE id = ?
        ");
        
        $stmt->bindValue(1,  $this->emptyToNull($data['center_id'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(2,  $this->emptyToNull($data['series_id'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(3,  $this->emptyToNull($data['variant_id'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(4,  $this->emptyToNull($data['body_type_id'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(5,  $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(6,  $data['condition'], PDO::PARAM_STR);
        $stmt->bindValue(7,  $this->toBool($data['is_approved_preowned'] ?? 0), PDO::PARAM_BOOL);
        $stmt->bindValue(8,  $data['model_year'], PDO::PARAM_INT);
        $stmt->bindValue(9,  $this->emptyToNull($data['fuel_type'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(10, $this->emptyToNull($data['power_kw'] ?? null), is_null($this->emptyToNull($data['power_kw'] ?? null)) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(11, $this->emptyToNull($data['power_hp'] ?? null), is_null($this->emptyToNull($data['power_hp'] ?? null)) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(12, $this->emptyToNull($data['transmission'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(13, $this->emptyToNull($data['drive_type'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(14, $this->emptyToNull($data['exterior_color'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(15, $this->emptyToNull($data['exterior_color_category'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(16, $this->emptyToNull($data['exterior_color_hex'] ?? null), PDO::PARAM_STR); // ← BARU
        $stmt->bindValue(17, $this->emptyToNull($data['interior_color'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(18, $this->emptyToNull($data['interior_color_hex'] ?? null), PDO::PARAM_STR); // ← BARU
        $stmt->bindValue(19, $this->emptyToNull($data['interior_material'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(20, $this->emptyToNull($data['seats'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(21, $data['price'], PDO::PARAM_INT);
        $stmt->bindValue(22, $data['main_image_url'], PDO::PARAM_STR);
        $stmt->bindValue(23, $this->emptyToNull($data['mileage'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(24, $this->emptyToNull($data['stock_number'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(25, $this->emptyToNull($data['vin'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(26, $this->emptyToNull($data['acceleration_0_100'] ?? null), is_null($this->emptyToNull($data['acceleration_0_100'] ?? null)) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(27, $this->emptyToNull($data['top_speed'] ?? null), PDO::PARAM_INT);
        $stmt->bindValue(28, $this->toBool($data['is_featured'] ?? 0), PDO::PARAM_BOOL);
        $stmt->bindValue(29, $this->toBool($data['is_active'] ?? 1), PDO::PARAM_BOOL);
        $stmt->bindValue(30, $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM vehicles WHERE id = ?");
        return $stmt->execute([$id]);
    }
}