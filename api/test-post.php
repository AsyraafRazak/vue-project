<?php
/**
 * DEBUG FILE — delete after testing, do NOT keep on production.
 * Visit: yourdomain.com/api/test-post.php
 * Checks config, PHPMailer, and actually sends a test email.
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

$configLocalPath = dirname(__DIR__, 2) . '/config.php';
$configHostPath  = dirname(__DIR__, 3) . '/config.php';

echo "<h3>Config check</h3>";
echo "Local path: $configLocalPath → " . (file_exists($configLocalPath) ? '<b style="color:green">EXISTS</b>' : '<b style="color:red">MISSING</b>') . "<br>";
echo "Hostinger path: $configHostPath → " . (file_exists($configHostPath) ? '<b style="color:green">EXISTS</b>' : '<b style="color:red">MISSING</b>') . "<br>";

$phpmailerPath = __DIR__ . '/PHPMailer/src/PHPMailer.php';
echo "<h3>PHPMailer check</h3>";
echo "Path: $phpmailerPath → " . (file_exists($phpmailerPath) ? '<b style="color:green">EXISTS</b>' : '<b style="color:red">MISSING</b>') . "<br>";

// Load config — try local first, then Hostinger path
if (file_exists($configLocalPath)) {
    require_once $configLocalPath;
    echo "<h3>Config loaded from: local path</h3>";
} elseif (file_exists($configHostPath)) {
    require_once $configHostPath;
    echo "<h3>Config loaded from: Hostinger path</h3>";
} else {
    echo "<h3 style='color:red'>✗ config.php not found at either path. Cannot continue.</h3>";
    exit();
}

echo "SMTP Host: " . MAIL_SMTP_HOST . "<br>";
echo "SMTP Port: " . MAIL_SMTP_PORT . "<br>";
echo "SMTP User: " . MAIL_SMTP_USERNAME . "<br>";
echo "SMTP Pass: " . (defined('MAIL_SMTP_PASSWORD') && MAIL_SMTP_PASSWORD !== 'xxxx xxxx xxxx xxxx' ? '<b style="color:green">SET</b>' : '<b style="color:red">NOT SET (still placeholder)</b>') . "<br>";
echo "Recipient: " . MAIL_RECIPIENT . "<br>";

// Load PHPMailer
if (!file_exists($phpmailerPath)) {
    echo "<h3 style='color:red'>✗ PHPMailer not found. Cannot send test email.</h3>";
    exit();
}

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

echo "<h3>PHPMailer loaded OK — attempting to send test email...</h3>";

$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = MAIL_SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_SMTP_USERNAME;
    $mail->Password   = MAIL_SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = MAIL_SMTP_PORT;
    $mail->SMTPDebug  = 0;

    // Disable SSL verification on local XAMPP only
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
    $mail->addReplyTo(MAIL_SMTP_USERNAME, 'Test');

    $mail->Subject = 'Test email from website server';
    $mail->Body    = "This is a test email sent from your server.\n\nIf you received this, your contact form email is working correctly.";

    $mail->send();
    echo "<h3 style='color:green'>✓ Email sent successfully! Check your inbox at " . MAIL_RECIPIENT . "</h3>";

} catch (Exception $e) {
    echo "<h3 style='color:red'>✗ SMTP Error:</h3>";
    echo "<pre>" . $mail->ErrorInfo . "</pre>";
}
