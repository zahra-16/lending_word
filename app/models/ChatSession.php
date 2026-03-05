<?php
// app/models/ChatSession.php

require_once __DIR__ . '/../Database.php';

class ChatSession {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /* ── Buat atau ambil sesi berdasarkan token ── */
    public function getOrCreate(string $token): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM chat_sessions WHERE session_token = ? LIMIT 1"
        );
        $stmt->execute([$token]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$session) {
            $stmt = $this->db->prepare(
                "INSERT INTO chat_sessions (session_token, status, started_at, last_message_at)
                 VALUES (?, 'open', NOW(), NOW())
                 RETURNING id"
            );
            $stmt->execute([$token]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $session = $this->getById((int)$row['id']);
        }

        return $session;
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM v_chat_sessions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getByToken(string $token): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM v_chat_sessions WHERE session_token = ? LIMIT 1"
        );
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /* ── Semua sesi untuk admin (diurutkan terbaru) ── */
    public function getAll(string $status = 'all'): array {
        if ($status === 'all') {
            $stmt = $this->db->query(
                "SELECT * FROM v_chat_sessions ORDER BY last_message_at DESC"
            );
        } else {
            $stmt = $this->db->prepare(
                "SELECT * FROM v_chat_sessions WHERE status = ?
                 ORDER BY last_message_at DESC"
            );
            $stmt->execute([$status]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ── Hitung sesi yang belum dibaca admin ── */
    public function countUnread(): int {
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM chat_sessions
             WHERE is_read_admin = 0 AND status != 'closed'"
        );
        return (int)$stmt->fetchColumn();
    }

    /* ── Update info pengunjung ── */
    public function updateVisitorInfo(string $token, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE chat_sessions
             SET visitor_name  = COALESCE(NULLIF(?, ''), visitor_name),
                 visitor_email = COALESCE(NULLIF(?, ''), visitor_email),
                 visitor_phone = COALESCE(NULLIF(?, ''), visitor_phone)
             WHERE session_token = ?"
        );
        return $stmt->execute([
            $data['name']  ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $token,
        ]);
    }

    /* ── Tandai sudah dibaca oleh admin ── */
    public function markReadAdmin(int $sessionId): bool {
        $stmt = $this->db->prepare(
            "UPDATE chat_sessions SET is_read_admin = 1 WHERE id = ?"
        );
        return $stmt->execute([$sessionId]);
    }

    /* ── Update timestamp pesan terakhir ── */
    public function touchLastMessage(int $sessionId): bool {
        $stmt = $this->db->prepare(
            "UPDATE chat_sessions
             SET last_message_at = NOW(),
                 is_read_admin   = 0
             WHERE id = ?"
        );
        return $stmt->execute([$sessionId]);
    }

    /* ── Ubah status sesi ── */
    public function setStatus(int $sessionId, string $status): bool {
        $stmt = $this->db->prepare(
            "UPDATE chat_sessions
             SET status    = ?,
                 closed_at = CASE WHEN ? = 'closed' THEN NOW() ELSE NULL END
             WHERE id = ?"
        );
        return $stmt->execute([$status, $status, $sessionId]);
    }

    /* ── Hapus sesi beserta semua pesannya (admin only) ── */
    public function deleteById(int $sessionId): bool {
        // Hapus semua pesan dulu (jika tidak ada ON DELETE CASCADE)
        $this->db->prepare("DELETE FROM chat_messages WHERE session_id = ?")
                 ->execute([$sessionId]);
        // Hapus sesinya
        $stmt = $this->db->prepare("DELETE FROM chat_sessions WHERE id = ?");
        return $stmt->execute([$sessionId]);
    }

    /* ── Expose DB connection ── */
    public function getDb(): PDO {
        return $this->db;
    }

    /* ── Generate token unik ── */
    public static function generateToken(): string {
        return bin2hex(random_bytes(24));
    }
}