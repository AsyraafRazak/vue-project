<?php
/**
 * Add Project (Admin)
 * Appends a new project to data/projects.json and stores its screenshot in /uploads.
 *
 * REQUIREMENTS:
 * - config.php placed OUTSIDE public_html (same location used by send-mail.php)
 *   must define: ADMIN_UPLOAD_PASSWORD
 *
 * LOCAL XAMPP:  c:\xampp\htdocs\config.php
 * HOSTINGER:    /home/your-username/config.php
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
$localPath     = dirname(__DIR__, 2) . '/config.php';  // XAMPP: htdocs/config.php
$hostingerPath = dirname(__DIR__, 3) . '/config.php';  // Hostinger: /home/user/domains/config.php

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

// ── Validate input (multipart/form-data: password, name, desc, url, tags, image) ──
$password = isset($_POST['password']) ? $_POST['password'] : '';
$name     = isset($_POST['name'])     ? trim(strip_tags($_POST['name']))    : '';
$desc     = isset($_POST['desc'])     ? trim(strip_tags($_POST['desc']))    : '';
$url      = isset($_POST['url'])      ? trim($_POST['url'])                : '';
$tagsRaw  = isset($_POST['tags'])     ? trim($_POST['tags'])               : '';

if (!hash_equals(ADMIN_UPLOAD_PASSWORD, $password)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    exit();
}

if (empty($name) || empty($desc) || empty($url)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name, description, and URL are required.']);
    exit();
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid URL.']);
    exit();
}

$tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
$tags = array_values($tags);
// ─────────────────────────────────────────────────────────────────────────────

// ── Handle image upload (optional) ──────────────────────────────────────────────
$imagePath = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowedTypes[$mimeType])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Image must be JPG, PNG, or WEBP.']);
        exit();
    }

    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Image must be under 5MB.']);
        exit();
    }

    $ext = $allowedTypes[$mimeType];
    $safeName = preg_replace('/[^a-z0-9-]/', '', strtolower(str_replace(' ', '-', $name)));
    $filename = $safeName . '-' . time() . '.' . $ext;

    $uploadsDir = dirname(__DIR__) . '/uploads/';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

    $destination = $uploadsDir . $filename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save image.']);
        exit();
    }

    $imagePath = '/uploads/' . $filename;
}
// ─────────────────────────────────────────────────────────────────────────────

// ── Append to projects.json ─────────────────────────────────────────────────────
$dataPath = __DIR__ . '/data/projects.json';

$projects = [];
if (file_exists($dataPath)) {
    $raw = file_get_contents($dataPath);
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $projects = $decoded;
    }
}

$newProject = [
    'name'  => $name,
    'desc'  => $desc,
    'url'   => $url,
    'tags'  => $tags,
    'image' => $imagePath, // null if no image was uploaded
];

$projects[] = $newProject;

$written = file_put_contents(
    $dataPath,
    json_encode($projects, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    LOCK_EX
);

if ($written === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to write projects.json.']);
    exit();
}

echo json_encode(['success' => true, 'message' => 'Project added.', 'project' => $newProject]);