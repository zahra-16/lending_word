<?php
require_once __DIR__ . '/../Database.php';

class SavedVehicle {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get or create session ID for tracking saved vehicles
     */
    public static function getSessionId() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['finder_session_id'])) {
            $_SESSION['finder_session_id'] = bin2hex(random_bytes(16));
        }
        
        return $_SESSION['finder_session_id'];
    }
    
    /**
     * Save a vehicle to favorites
     */
    public function save($vehicleId, $userId = null) {
        $sessionId = self::getSessionId();
        
        // Get current price
        $stmt = $this->db->prepare("SELECT price FROM vehicles WHERE id = ?");
        $stmt->execute([$vehicleId]);
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$vehicle) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO saved_vehicles (session_id, user_id, vehicle_id, saved_price) 
                VALUES (?, ?, ?, ?)
                ON CONFLICT (session_id, vehicle_id) DO NOTHING
                RETURNING id
            ");
            $stmt->execute([$sessionId, $userId, $vehicleId, $vehicle['price']]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            error_log("Error saving vehicle: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove a vehicle from favorites
     */
    public function unsave($vehicleId, $userId = null) {
        $sessionId = self::getSessionId();
        
        $stmt = $this->db->prepare("
            DELETE FROM saved_vehicles 
            WHERE vehicle_id = ? AND session_id = ?
        ");
        return $stmt->execute([$vehicleId, $sessionId]);
    }
    
    /**
     * Check if a vehicle is saved
     */
    public function isSaved($vehicleId, $userId = null) {
        $sessionId = self::getSessionId();
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM saved_vehicles 
            WHERE vehicle_id = ? AND session_id = ?
        ");
        $stmt->execute([$vehicleId, $sessionId]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Get all saved vehicles for current session
     */
    public function getAllSaved($userId = null) {
        $sessionId = self::getSessionId();
        
        $stmt = $this->db->prepare("
            SELECT * FROM v_saved_vehicles 
            WHERE session_id = ?
            ORDER BY saved_at DESC
        ");
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get count of saved vehicles
     */
    public function getSavedCount($userId = null) {
        $sessionId = self::getSessionId();
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM saved_vehicles 
            WHERE session_id = ?
        ");
        $stmt->execute([$sessionId]);
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Get saved vehicle IDs (for quick checking)
     */
    public function getSavedIds($userId = null) {
        $sessionId = self::getSessionId();
        
        $stmt = $this->db->prepare("
            SELECT vehicle_id FROM saved_vehicles 
            WHERE session_id = ?
        ");
        $stmt->execute([$sessionId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'vehicle_id');
    }
    
    /**
     * Clear all saved vehicles for current session
     */
    public function clearAll($userId = null) {
        $sessionId = self::getSessionId();
        
        $stmt = $this->db->prepare("
            DELETE FROM saved_vehicles WHERE session_id = ?
        ");
        return $stmt->execute([$sessionId]);
    }
    
    /**
     * Get vehicles for comparison (max 3)
     */
    public function getComparisonVehicles($vehicleIds) {
        if (empty($vehicleIds) || !is_array($vehicleIds)) {
            return [];
        }
        
        $vehicleIds = array_slice($vehicleIds, 0, 3); // Max 3 vehicles
        $placeholders = str_repeat('?,', count($vehicleIds) - 1) . '?';
        
        $stmt = $this->db->prepare("
            SELECT * FROM v_vehicles_complete 
            WHERE id IN ($placeholders)
            ORDER BY CASE id " . 
            implode(' ', array_map(fn($i, $id) => "WHEN ? THEN $i", array_keys($vehicleIds), $vehicleIds)) . 
            " END
        ");
        $stmt->execute($vehicleIds);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Admin: Get all saved vehicles with statistics
     */
    public function getAdminStatistics() {
        $stmt = $this->db->query("
            SELECT 
                v.id,
                v.title,
                v.save_count,
                v.last_saved_at,
                COUNT(DISTINCT sv.session_id) as unique_savers
            FROM vehicles v
            LEFT JOIN saved_vehicles sv ON v.id = sv.vehicle_id
            WHERE v.save_count > 0
            GROUP BY v.id, v.title, v.save_count, v.last_saved_at
            ORDER BY v.save_count DESC, v.last_saved_at DESC
            LIMIT 50
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Admin: Get recent saves
     */
    public function getRecentSaves($limit = 20) {
        $stmt = $this->db->prepare("
            SELECT 
                sv.*,
                v.title as vehicle_title,
                v.condition,
                v.model_year
            FROM saved_vehicles sv
            JOIN vehicles v ON sv.vehicle_id = v.id
            ORDER BY sv.saved_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}