<?php
/**
 * apply.php — Handler form lamaran career page
 * Letakkan di: /lending_word/apply.php
 */
session_start();
require_once __DIR__ . '/app/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$jobId        = (int)($_POST['job_id']        ?? 0);
$fullName     = trim($_POST['full_name']       ?? '');
$email        = trim($_POST['email']           ?? '');
$phone        = trim($_POST['phone']           ?? '');
$linkedinUrl  = trim($_POST['linkedin_url']    ?? '');
$portfolioUrl = trim($_POST['portfolio_url']   ?? '');
$coverLetter  = trim($_POST['cover_letter']    ?? '');

// Validasi wajib
if (!$fullName || !$email) {
    echo json_encode(['success' => false, 'message' => 'Nama dan email wajib diisi.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid.']);
    exit;
}

$ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR']
    ?? $_SERVER['HTTP_X_REAL_IP']
    ?? $_SERVER['REMOTE_ADDR']
    ?? null;
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

try {
    $db = Database::getInstance()->getConnection();

    if ($jobId > 0) {
        $chk = $db->prepare("SELECT id FROM career_jobs WHERE id = ? AND is_active = true");
        $chk->execute([$jobId]);
        if (!$chk->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Posisi tidak ditemukan atau sudah tidak aktif.']);
            exit;
        }
    }

    if ($jobId > 0) {
        $dup = $db->prepare(
            "SELECT id FROM career_applications
             WHERE job_id = ? AND email = ?
               AND created_at >= NOW() - INTERVAL '7 days'
             LIMIT 1"
        );
        $dup->execute([$jobId, $email]);
        if ($dup->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Anda sudah melamar posisi ini dalam 7 hari terakhir.']);
            exit;
        }
    }

    $stmt = $db->prepare(
        "INSERT INTO career_applications
         (job_id, full_name, email, phone, linkedin_url, portfolio_url,
          cover_letter, status, source, ip_address, user_agent, created_at, updated_at)
         VALUES
         (:job_id, :full_name, :email, :phone, :linkedin_url, :portfolio_url,
          :cover_letter, 'new', 'website', :ip_address, :user_agent, NOW(), NOW())"
    );

    $stmt->execute([
        'job_id'        => $jobId > 0 ? $jobId : null,
        'full_name'     => $fullName,
        'email'         => $email,
        'phone'         => $phone        ?: null,
        'linkedin_url'  => $linkedinUrl  ?: null,
        'portfolio_url' => $portfolioUrl ?: null,
        'cover_letter'  => $coverLetter  ?: null,
        'ip_address'    => $ipAddress,
        'user_agent'    => $userAgent,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Lamaran Anda berhasil dikirim! Tim HR kami akan meninjau dan menghubungi Anda segera.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan server. Silakan coba lagi.']);
}