<?php
// admin/chat-api.php
// Endpoint AJAX untuk widget chat di frontend (visitor-facing)

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/models/ChatSession.php';
require_once __DIR__ . '/../app/models/ChatMessage.php';

$sessionModel = new ChatSession();
$messageModel = new ChatMessage();
$action       = $_REQUEST['action'] ?? '';

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

function getOrCreateToken(): string {
    if (!empty($_COOKIE['porsche_chat_token'])) {
        $tok = $_COOKIE['porsche_chat_token'];
        if (preg_match('/^[a-f0-9]{48}$/', $tok)) return $tok;
    }
    $tok = ChatSession::generateToken();
    setcookie('porsche_chat_token', $tok, [
        'expires'  => time() + 60 * 60 * 24 * 90,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    return $tok;
}

function setNewToken(string $tok): void {
    setcookie('porsche_chat_token', $tok, [
        'expires'  => time() + 60 * 60 * 24 * 90,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function sendWelcomeMessage(ChatMessage $messageModel, int $sessionId): void {
    $welcomeText = "Thanks for reaching out to us. We'll be right with you. Alternatively, leave us your contact number and our sales consultants will reach out to you directly.";
    $messageModel->send($sessionId, 'admin', $welcomeText, 'Porsche Customer Care');
}

try {
    switch ($action) {

        case 'init': {
            $token   = getOrCreateToken();
            $session = $sessionModel->getOrCreate($token);
            $messages = $messageModel->getBySession($session['id']);
            $messageModel->markAdminMessagesRead($session['id']);

            if (empty($messages)) {
                sendWelcomeMessage($messageModel, $session['id']);
                $messages = $messageModel->getBySession($session['id']);
            }

            jsonOk([
                'session_id'   => $session['id'],
                'token'        => $token,
                'status'       => $session['status'],
                'visitor_name' => $session['visitor_name'],
                'messages'     => $messages,
                'last_id'      => $messageModel->getLastId($session['id']),
            ]);
        }

        case 'new_session': {
            $tok = ChatSession::generateToken();
            setNewToken($tok);
            $session = $sessionModel->getOrCreate($tok);
            sendWelcomeMessage($messageModel, $session['id']);
            $messages = $messageModel->getBySession($session['id']);

            jsonOk([
                'session_id'   => $session['id'],
                'token'        => $tok,
                'status'       => $session['status'],
                'visitor_name' => $session['visitor_name'],
                'messages'     => $messages,
                'last_id'      => $messageModel->getLastId($session['id']),
                'is_new'       => true,
            ]);
        }

        case 'switch_session': {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonErr('Method not allowed', 405);
            $body  = json_decode(file_get_contents('php://input'), true) ?? [];
            $token = trim($body['token'] ?? '');
            if (!preg_match('/^[a-f0-9]{48}$/', $token)) jsonErr('Token tidak valid');
            $session = $sessionModel->getByToken($token);
            if (!$session) jsonErr('Sesi tidak ditemukan', 404);
            setNewToken($token);
            $messages = $messageModel->getBySession($session['id']);
            $messageModel->markAdminMessagesRead($session['id']);
            jsonOk([
                'session_id'   => $session['id'],
                'token'        => $token,
                'status'       => $session['status'],
                'visitor_name' => $session['visitor_name'],
                'messages'     => $messages,
                'last_id'      => $messageModel->getLastId($session['id']),
            ]);
        }

        case 'sessions_by_tokens': {
            $body   = json_decode(file_get_contents('php://input'), true) ?? [];
            $tokens = $body['tokens'] ?? [];
            $tokens = array_values(array_filter(array_slice((array)$tokens, 0, 20), function($t) {
                return preg_match('/^[a-f0-9]{48}$/', $t);
            }));
            if (empty($tokens)) { jsonOk(['sessions' => []]); }
            $placeholders = implode(',', array_fill(0, count($tokens), '?'));
            $db = $sessionModel->getDb();
            $stmt = $db->prepare(
                "SELECT cs.id, cs.session_token, cs.status, cs.visitor_name,
                        cs.last_message_at,
                        (SELECT message FROM chat_messages
                         WHERE session_id = cs.id
                         ORDER BY sent_at DESC LIMIT 1) AS last_message,
                        (SELECT COUNT(*) FROM chat_messages
                         WHERE session_id = cs.id AND sender_type = 'admin' AND is_read = 0) AS unread_count
                 FROM chat_sessions cs
                 WHERE cs.session_token IN ({$placeholders})
                 ORDER BY cs.last_message_at DESC"
            );
            $stmt->execute($tokens);
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            jsonOk(['sessions' => $sessions]);
        }

        case 'send': {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonErr('Method not allowed', 405);
            $token   = getOrCreateToken();
            $body    = json_decode(file_get_contents('php://input'), true) ?? [];
            $message = sanitize($body['message'] ?? '');
            $name    = sanitize($body['name']    ?? '');
            $email   = sanitize($body['email']   ?? '');
            $phone   = sanitize($body['phone']   ?? '');
            if ($message === '') jsonErr('Pesan tidak boleh kosong');
            $session = $sessionModel->getOrCreate($token);
            if ($session['status'] === 'closed') jsonErr('Sesi chat sudah ditutup');
            if ($name || $email || $phone) {
                $sessionModel->updateVisitorInfo($token, [
                    'name'  => $name  ?: null,
                    'email' => $email ?: null,
                    'phone' => $phone ?: null,
                ]);
            }
            $senderName = $name ?: ($session['visitor_name'] ?? 'Visitor');
            $msg = $messageModel->send($session['id'], 'visitor', $message, $senderName);
            $sessionModel->touchLastMessage($session['id']);
            jsonOk(['message' => $msg]);
        }

        case 'poll': {
            $token = $_COOKIE['porsche_chat_token'] ?? '';
            if (!$token) jsonErr('Tidak ada sesi', 404);
            $session = $sessionModel->getByToken($token);
            if (!$session) jsonErr('Sesi tidak ditemukan', 404);
            $afterId  = (int)($_GET['after_id'] ?? 0);
            $messages = $messageModel->getBySession($session['id'], $afterId ?: null);
            if ($messages) {
                $messageModel->markAdminMessagesRead($session['id']);
            }
            jsonOk([
                'messages' => $messages,
                'status'   => $session['status'],
                'last_id'  => $messageModel->getLastId($session['id']),
            ]);
        }

        case 'contact': {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonErr('Method not allowed', 405);
            $token = getOrCreateToken();
            $body  = json_decode(file_get_contents('php://input'), true) ?? [];
            $name  = sanitize($body['name']  ?? '');
            $email = sanitize($body['email'] ?? '');
            $phone = sanitize($body['phone'] ?? '');
            if (!$name || !$email) jsonErr('Nama dan email wajib diisi');
            $sessionModel->updateVisitorInfo($token, [
                'name'  => $name,
                'email' => $email,
                'phone' => $phone ?: null,
            ]);
            $session = $sessionModel->getByToken($token);
            if ($session) {
                $notifMsg = "📋 Visitor meninggalkan kontak:\n• Nama: {$name}\n• Email: {$email}" . ($phone ? "\n• Phone: {$phone}" : '');
                $messageModel->send($session['id'], 'admin', $notifMsg, 'System');
                $sessionModel->touchLastMessage($session['id']);
            }
            jsonOk(['saved' => true]);
        }

        default:
            jsonErr('Action tidak dikenal', 400);
    }

} catch (Throwable $e) {
    error_log('[Chat API Error] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    jsonErr('Terjadi kesalahan server: ' . $e->getMessage(), 500);
}