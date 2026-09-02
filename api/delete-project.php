<?php
/**
 * Delete Project (Admin)
 * Removes a project (matched by name) from data/projects.json.
 * Also deletes its screenshot from /uploads if one was stored.
 *
 * REQUIREMENTS: same config.php as upload-project.php (ADMIN_UPLOAD_PASSWORD)
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

// ── Validate input (JSON body: password, name) ──────────────────────────────────
$body = json_decode(file_get_contents('php://input'), true);

$password = isset($body['password']) ? $body['password'] : '';
$name     = isset($body['name'])     ? trim($body['name']) : '';

if (!hash_equals(ADMIN_UPLOAD_PASSWORD, $password)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    exit();
}

if (empty($name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Project name is required.']);
    exit();
}
// ─────────────────────────────────────────────────────────────────────────────

// ── Remove from projects.json ───────────────────────────────────────────────────
$dataPath = __DIR__ . '/data/projects.json';

if (!file_exists($dataPath)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'projects.json not found.']);
    exit();
}

$raw = file_get_contents($dataPath);
$projects = json_decode($raw, true);

if (!is_array($projects)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'projects.json is invalid.']);
    exit();
}

$found = false;
$remaining = [];
$imageToDelete = null;

foreach ($projects as $project) {
    if (isset($project['name']) && $project['name'] === $name && !$found) {
        $found = true;
        if (!empty($project['image'])) {
            $imageToDelete = $project['image'];
        }
        continue; // skip adding this one — it's the one being deleted
    }
    $remaining[] = $project;
}

if (!$found) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'No project found with that name.']);
    exit();
}

$written = file_put_contents(
    $dataPath,
    json_encode($remaining, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    LOCK_EX
);

if ($written === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update projects.json.']);
    exit();
}

// ── Delete the screenshot file, if any ──────────────────────────────────────────
if ($imageToDelete) {
    $imageFile = dirname(__DIR__) . $imageToDelete; // image was stored as "/uploads/xxx.jpg"
    if (file_exists($imageFile)) {
        @unlink($imageFile);
    }
}
// ─────────────────────────────────────────────────────────────────────────────

echo json_encode(['success' => true, 'message' => 'Project deleted.']);