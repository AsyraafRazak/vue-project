<?php
/**
 * Verify Admin Password
 * Used only by the admin panel's login gate to check the password before
 * showing the CMS UI. Does not modify anything.
 *
 * Expects JSON body: { "password": "..." }
 *
 * REQUIREMENTS: same config.php as the other admin endpoints (ADMIN_UPLOAD_PASSWORD)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

// ── Load config ───────────────────────────────────────────────────────────────
$localPath     = dirname(__DIR__, 2) . '/config.php';
$hostingerPath = dirname(__DIR__, 3) . '/config.php';

if (file_exists($localPath)) {
    require_once $localPath;
} elseif (file_exists($hostingerPath)) {
    require_once $hostingerPath;
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server configuration error.']);
    exit();
}

if (!defined('ADMIN_UPLOAD_PASSWORD')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'ADMIN_UPLOAD_PASSWORD not configured.']);
    exit();
}
// ─────────────────────────────────────────────────────────────────────────────

$input = json_decode(file_get_contents('php://input'), true);
$password = isset($input['password']) ? $input['password'] : '';

if (!hash_equals(ADMIN_UPLOAD_PASSWORD, $password)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    exit();
}

echo json_encode(['success' => true]);