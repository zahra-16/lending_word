<?php
// admin/chat-admin-api.php

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

require_once __DIR__ . '/../app/models/Admin.php';
require_once __DIR__ . '/../app/models/ChatSession.php';
require_once __DIR__ . '/../app/models/ChatMessage.php';

session_start();
if (!Admin::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

$sessionModel = new ChatSession();
$messageModel = new ChatMessage();
$action       = $_GET['action'] ?? $_POST['action'] ?? '';

function jsonOk(array $data = []): void {
    echo json_encode(['ok' => true] + $data);
    exit;
}
function jsonErr(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}
function sanitize(string $val): string {
    return mb_substr(trim(strip_tags($val)), 0, 2000);
}

try {
    switch ($action) {

        /* ── sessions ── */
        case 'sessions': {
            $status   = $_GET['status'] ?? 'all';
            $sessions = $sessionModel->getAll($status);
            jsonOk([
                'sessions'     => $sessions,
                'unread_count' => $sessionModel->countUnread(),
            ]);
        }

        /* ── messages ── */
        case 'messages': {
            $sessionId = (int)($_GET['session_id'] ?? 0);
            if (!$sessionId) jsonErr('session_id diperlukan');

            $session = $sessionModel->getById($sessionId);
            if (!$session) jsonErr('Sesi tidak ditemukan', 404);

            $messages = $messageModel->getBySession($sessionId);
            $messageModel->markVisitorMessagesRead($sessionId);
            $sessionModel->markReadAdmin($sessionId);

            jsonOk([
                'session'  => $session,
                'messages' => $messages,
                'last_id'  => $messageModel->getLastId($sessionId),
            ]);
        }

        /* ── reply ── */
        case 'reply': {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonErr('Method not allowed', 405);
            $body      = json_decode(file_get_contents('php://input'), true) ?? [];
            $sessionId = (int)($body['session_id'] ?? 0);
            $message   = sanitize($body['message'] ?? '');

            if (!$sessionId) jsonErr('session_id diperlukan');
            if ($message === '') jsonErr('Pesan tidak boleh kosong');

            $session = $sessionModel->getById($sessionId);
            if (!$session) jsonErr('Sesi tidak ditemukan', 404);
            if ($session['status'] === 'closed') jsonErr('Sesi sudah ditutup');

            $adminName = $_SESSION['admin_name'] ?? 'Porsche Admin';
            $msg = $messageModel->send($sessionId, 'admin', $message, $adminName);

            $db = Database::getInstance()->getConnection();
            $db->prepare("UPDATE chat_sessions SET last_message_at = NOW() WHERE id = ?")
               ->execute([$sessionId]);

            jsonOk(['message' => $msg]);
        }

        /* ── delete_message : hapus satu pesan ── */
        case 'delete_message': {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonErr('Method not allowed', 405);
            $body      = json_decode(file_get_contents('php://input'), true) ?? [];
            $messageId = (int)($body['message_id'] ?? 0);

            if (!$messageId) jsonErr('message_id diperlukan');

            $msg = $messageModel->getById($messageId);
            if (!$msg) jsonErr('Pesan tidak ditemukan', 404);

            if (!$messageModel->deleteById($messageId)) jsonErr('Gagal menghapus pesan');

            $sessionId = (int)$msg['session_id'];
            if ($messageModel->getLastId($sessionId) === 0) {
                $db = Database::getInstance()->getConnection();
                $db->prepare("UPDATE chat_sessions SET last_message_at = started_at WHERE id = ?")
                   ->execute([$sessionId]);
            }

            jsonOk(['deleted_id' => $messageId]);
        }

        /* ── delete_session : hapus seluruh percakapan dari list ── */
        case 'delete_session': {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonErr('Method not allowed', 405);
            $body      = json_decode(file_get_contents('php://input'), true) ?? [];
            $sessionId = (int)($body['session_id'] ?? 0);

            if (!$sessionId) jsonErr('session_id diperlukan');

            $session = $sessionModel->getById($sessionId);
            if (!$session) jsonErr('Sesi tidak ditemukan', 404);

            if (!$sessionModel->deleteById($sessionId)) jsonErr('Gagal menghapus percakapan');

            jsonOk(['deleted_session_id' => $sessionId]);
        }

        /* ── set_status ── */
        case 'set_status': {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonErr('Method not allowed', 405);
            $body      = json_decode(file_get_contents('php://input'), true) ?? [];
            $sessionId = (int)($body['session_id'] ?? 0);
            $status    = $body['status'] ?? '';

            if (!in_array($status, ['open', 'pending', 'closed'], true)) jsonErr('Status tidak valid');
            if (!$sessionId) jsonErr('session_id diperlukan');

            $sessionModel->setStatus($sessionId, $status);
            jsonOk(['status' => $status]);
        }

        /* ── poll_admin ── */
        case 'poll_admin': {
            $sessionId = (int)($_GET['session_id'] ?? 0);
            $afterId   = (int)($_GET['after_id']   ?? 0);
            if (!$sessionId) jsonErr('session_id diperlukan');

            $messages = $messageModel->getBySession($sessionId, $afterId);
            if ($messages) {
                $messageModel->markVisitorMessagesRead($sessionId);
                $sessionModel->markReadAdmin($sessionId);
            }

            jsonOk([
                'messages'     => $messages,
                'last_id'      => $messageModel->getLastId($sessionId),
                'unread_count' => $sessionModel->countUnread(),
            ]);
        }

        default:
            jsonErr('Action tidak dikenal', 400);
    }

} catch (Throwable $e) {
    error_log('[Admin Chat API Error] ' . $e->getMessage());
    jsonErr('Terjadi kesalahan server', 500);
}