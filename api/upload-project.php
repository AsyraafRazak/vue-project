<?php
/**
 * Add Project (Admin)
 * Appends a new project to data/projects.json and stores its photos in /uploads.
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

// ── Validate input (multipart/form-data: password, name, desc, url, tags, mainImage, galleryImages[]) ──
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

// ── Handle photo uploads (1 main, required + up to 4 gallery, optional) ────────
$allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$maxBytes     = 5 * 1024 * 1024;

$uploadsDir = dirname(__DIR__) . '/demoimage/';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

$safeName = preg_replace('/[^a-z0-9-]/', '', strtolower(str_replace(' ', '-', $name)));

/**
 * Validate + move a single uploaded file. Returns the public path (e.g. /demoimage/x.jpg)
 * on success, or null on failure.
 */
function saveOneImage($tmpName, $size, $allowedTypes, $maxBytes, $uploadsDir, $safeName, $suffix) {
    if ($size > $maxBytes) {
        return null;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tmpName);
    finfo_close($finfo);

    if (!isset($allowedTypes[$mimeType])) {
        return null;
    }

    $ext = $allowedTypes[$mimeType];
    $filename = $safeName . '-' . $suffix . '-' . time() . '-' . mt_rand(1000, 9999) . '.' . $ext;
    $destination = $uploadsDir . $filename;

    if (!move_uploaded_file($tmpName, $destination)) {
        return null;
    }

    return '/demoimage/' . $filename;
}

$images = [];

// Main photo (required)
if (!isset($_FILES['mainImage']) || $_FILES['mainImage']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Main photo is required.']);
    exit();
}

$mainPath = saveOneImage(
    $_FILES['mainImage']['tmp_name'],
    $_FILES['mainImage']['size'],
    $allowedTypes,
    $maxBytes,
    $uploadsDir,
    $safeName,
    'main'
);

if (!$mainPath) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Main photo must be JPG, PNG, or WEBP under 5MB.']);
    exit();
}

$images[] = $mainPath;

// Gallery photos (optional, max 4)
if (isset($_FILES['galleryImages']) && is_array($_FILES['galleryImages']['tmp_name'])) {
    $count = count($_FILES['galleryImages']['tmp_name']);

    for ($i = 0; $i < min($count, 4); $i++) {
        if ($_FILES['galleryImages']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }

        $extraPath = saveOneImage(
            $_FILES['galleryImages']['tmp_name'][$i],
            $_FILES['galleryImages']['size'][$i],
            $allowedTypes,
            $maxBytes,
            $uploadsDir,
            $safeName,
            'gallery-' . ($i + 1)
        );

        if ($extraPath) {
            $images[] = $extraPath;
        }
    }
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
    'name'   => $name,
    'desc'   => $desc,
    'url'    => $url,
    'tags'   => $tags,
    'images' => $images, // images[0] is the main photo, rest are the hover-collage extras
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