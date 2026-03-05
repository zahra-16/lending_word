<?php
// app/models/ChatMessage.php

require_once __DIR__ . '/../Database.php';

class ChatMessage {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /* ── Kirim pesan baru ── */
    public function send(int $sessionId, string $senderType, string $message, ?string $senderName = null): ?array {
        $stmt = $this->db->prepare(
            "INSERT INTO chat_messages (session_id, sender_type, sender_name, message, sent_at)
             VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$sessionId, $senderType, $senderName, trim($message)]);
        $id = (int)$this->db->lastInsertId();
        if (!$id) return null;
        return $this->getById($id);
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM chat_messages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /* ── Ambil semua pesan dalam satu sesi ── */
    public function getBySession(int $sessionId, ?int $afterId = null): array {
        if ($afterId) {
            $stmt = $this->db->prepare(
                "SELECT * FROM chat_messages
                 WHERE session_id = ? AND id > ?
                 ORDER BY sent_at ASC"
            );
            $stmt->execute([$sessionId, $afterId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT * FROM chat_messages
                 WHERE session_id = ?
                 ORDER BY sent_at ASC"
            );
            $stmt->execute([$sessionId]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ── Hapus pesan berdasarkan ID (admin only) ── */
    public function deleteById(int $messageId): bool {
        $stmt = $this->db->prepare("DELETE FROM chat_messages WHERE id = ?");
        return $stmt->execute([$messageId]);
    }

    /* ── Tandai semua pesan visitor sebagai sudah dibaca (oleh admin) ── */
    public function markVisitorMessagesRead(int $sessionId): bool {
        $stmt = $this->db->prepare(
            "UPDATE chat_messages
             SET is_read = 1
             WHERE session_id = ? AND sender_type = 'visitor' AND is_read = 0"
        );
        return $stmt->execute([$sessionId]);
    }

    /* ── Tandai semua pesan admin sebagai sudah dibaca (oleh visitor) ── */
    public function markAdminMessagesRead(int $sessionId): bool {
        $stmt = $this->db->prepare(
            "UPDATE chat_messages
             SET is_read = 1
             WHERE session_id = ? AND sender_type = 'admin' AND is_read = 0"
        );
        return $stmt->execute([$sessionId]);
    }

    /* ── Hitung pesan admin yang belum dibaca di sesi ini ── */
    public function countUnreadAdmin(int $sessionId): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM chat_messages
             WHERE session_id = ? AND sender_type = 'admin' AND is_read = 0"
        );
        $stmt->execute([$sessionId]);
        return (int)$stmt->fetchColumn();
    }

    /* ── ID pesan terbaru di sesi ── */
    public function getLastId(int $sessionId): int {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(MAX(id), 0) FROM chat_messages WHERE session_id = ?"
        );
        $stmt->execute([$sessionId]);
        return (int)$stmt->fetchColumn();
    }
}