<?php
/**
 * Contact Form Mailer
 * Uses Gmail SMTP via PHPMailer.
 *
 * REQUIREMENTS:
 * - PHPMailer at: api/PHPMailer/src/
 * - config.php placed OUTSIDE public_html (one level above public_html on Hostinger)
 *
 * LOCAL XAMPP:  c:\xampp\htdocs\config.php
 * HOSTINGER:    /home/your-username/config.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

// ── Load config ───────────────────────────────────────────────────────────────
$localPath     = dirname(__DIR__, 2) . '/config.php';  // XAMPP: htdocs/config.php
$hostingerPath = dirname(__DIR__, 3) . '/config.php';  // Hostinger: /home/user/config.php

if (file_exists($localPath)) {
    require_once $localPath;
} elseif (file_exists($hostingerPath)) {
    require_once $hostingerPath;
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server configuration error.']);
    exit();
}
// ─────────────────────────────────────────────────────────────────────────────

// ── Load PHPMailer ────────────────────────────────────────────────────────────
$phpmailerPath = __DIR__ . '/PHPMailer/src/PHPMailer.php';

if (!file_exists($phpmailerPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Mailer library not found.']);
    exit();
}

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
// ─────────────────────────────────────────────────────────────────────────────

// ── Validate input ────────────────────────────────────────────────────────────
$body = json_decode(file_get_contents('php://input'), true);

$name    = isset($body['name'])    ? trim(strip_tags($body['name']))    : '';
$email   = isset($body['email'])   ? trim($body['email'])               : '';
$message = isset($body['message']) ? trim(strip_tags($body['message'])) : '';

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit();
}

$email = filter_var($email, FILTER_SANITIZE_EMAIL);
// ─────────────────────────────────────────────────────────────────────────────

// ── Send via Gmail SMTP ───────────────────────────────────────────────────────
$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = MAIL_SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_SMTP_USERNAME;
    $mail->Password   = MAIL_SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = MAIL_SMTP_PORT;

    // Disable SSL verification on local XAMPP (no CA certs available)
    // MAIL_SMTP_VERIFY_SSL is only defined in local config — production skips this block
    if (defined('MAIL_SMTP_VERIFY_SSL') && MAIL_SMTP_VERIFY_SSL === false) {
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ]
        ];
    }

    $mail->setFrom(MAIL_SMTP_USERNAME, MAIL_FROM_NAME);
    $mail->addAddress(MAIL_RECIPIENT);
    $mail->addReplyTo($email, $name);

    $mail->Subject = "New inquiry from $name";
    $mail->Body    =
        "You received a new inquiry from your website contact form.\n\n" .
        "Name:    $name\n" .
        "Email:   $email\n\n" .
        "Message:\n$message";

    $mail->send();

    echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again.']);
}
