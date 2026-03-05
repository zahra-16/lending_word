<?php
require_once __DIR__ . '/../Database.php';

class PorscheCenterHours {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all opening hours for a center, ordered Mon–Sun
     */
    public function getByCenter(int $centerId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM porsche_center_hours
            WHERE center_id = ?
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->execute([$centerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Upsert (insert or update) a single day's hours
     */
    public function upsert(int $centerId, array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO porsche_center_hours
                (center_id, day_name, is_closed, open_time, close_time, lunch_start, lunch_end, sort_order)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON CONFLICT (center_id, day_name) DO UPDATE SET
                is_closed   = EXCLUDED.is_closed,
                open_time   = EXCLUDED.open_time,
                close_time  = EXCLUDED.close_time,
                lunch_start = EXCLUDED.lunch_start,
                lunch_end   = EXCLUDED.lunch_end,
                sort_order  = EXCLUDED.sort_order
        ");

        return $stmt->execute([
            $centerId,
            $data['day_name'],
            ($data['is_closed'] ?? false) ? 1 : 0,
            $this->emptyToNull($data['open_time']   ?? null),
            $this->emptyToNull($data['close_time']  ?? null),
            $this->emptyToNull($data['lunch_start'] ?? null),
            $this->emptyToNull($data['lunch_end']   ?? null),
            (int)($data['sort_order'] ?? 0),
        ]);
    }

    /**
     * Bulk update all hours for a center from POST data
     * Expects $rows = [ ['day_name'=>'Monday', 'is_closed'=>0, 'open_time'=>'08.30', ...], ... ]
     */
    public function bulkUpdate(int $centerId, array $rows): bool {
        foreach ($rows as $row) {
            $row['center_id'] = $centerId;
            $this->upsert($centerId, $row);
        }
        return true;
    }

    private function emptyToNull($value) {
        return ($value === '' || $value === null) ? null : $value;
    }
}