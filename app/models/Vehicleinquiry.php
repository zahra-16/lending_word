<?php
require_once __DIR__ . '/../Database.php';

class VehicleInquiry {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new inquiry from the contact form
     */
    public function create(array $data): int|false {
        $stmt = $this->db->prepare("
            INSERT INTO vehicle_inquiries (
                vehicle_id, center_id, inquiry_type,
                salutation, first_name, last_name,
                email, phone_country_code, phone_number,
                message, callback_time,
                ip_address, user_agent, privacy_agreed
            ) VALUES (
                ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?, ?,
                ?, ?, ?
            )
        ");

        $result = $stmt->execute([
            $this->emptyToNull($data['vehicle_id']  ?? null),
            $this->emptyToNull($data['center_id']   ?? null),
            $data['inquiry_type']  ?? 'message',
            $this->emptyToNull($data['salutation']  ?? null),
            trim($data['first_name'] ?? ''),
            trim($data['last_name']  ?? ''),
            $this->emptyToNull($data['email']             ?? null),
            $this->emptyToNull($data['phone_country_code']?? null),
            $this->emptyToNull($data['phone_number']      ?? null),
            $this->emptyToNull($data['message']           ?? null),
            $this->emptyToNull($data['callback_time']     ?? null),
            $data['ip_address']    ?? null,
            $data['user_agent']    ?? null,
            (bool)($data['privacy_agreed'] ?? false),
        ]);

        return $result ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * Get all inquiries (for admin list)
     */
    public function getAll(array $filters = []): array {
        $sql    = "SELECT * FROM v_inquiries_complete WHERE 1=1";
        $params = [];

        if (!empty($filters['center_id'])) {
            $sql     .= " AND center_id = ?";
            $params[] = $filters['center_id'];
        }

        // FIX: hanya tambahkan kondisi is_read jika nilainya 'read' atau 'unread'
        if (isset($filters['is_read']) && $filters['is_read'] !== '') {
            if ($filters['is_read'] === 'unread') {
                $sql     .= " AND is_read = FALSE";
            } elseif ($filters['is_read'] === 'read') {
                $sql     .= " AND is_read = TRUE";
            }
            // jika nilai lain (misal 'all' atau kosong), tidak tambahkan kondisi
        }

        if (!empty($filters['inquiry_type'])) {
            $sql     .= " AND inquiry_type = ?";
            $params[] = $filters['inquiry_type'];
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single inquiry by ID
     */
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM v_inquiries_complete WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Count unread inquiries (for admin badge)
     */
    public function countUnread(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM vehicle_inquiries WHERE is_read = FALSE");
        return (int)$stmt->fetchColumn();
    }

    /**
     * Mark an inquiry as read
     */
    public function markRead(int $id): bool {
        $stmt = $this->db->prepare(
            "UPDATE vehicle_inquiries SET is_read = TRUE, updated_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Mark replied and add notes
     */
    public function markReplied(int $id, string $notes = ''): bool {
        $stmt = $this->db->prepare(
            "UPDATE vehicle_inquiries SET is_replied = TRUE, reply_notes = ?, updated_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$notes, $id]);
    }

    /**
     * Delete an inquiry
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM vehicle_inquiries WHERE id = ?");
        return $stmt->execute([$id]);
    }

    private function emptyToNull($value) {
        return ($value === '' || $value === null) ? null : $value;
    }
}