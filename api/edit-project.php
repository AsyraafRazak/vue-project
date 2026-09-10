<?php
/**
 * Edit Project (Admin)
 * Updates an existing project's fields and/or photos in data/projects.json.
 *
 * Expects multipart/form-data:
 *   password, originalName (which project to update), name, desc, url, tags,
 *   mainImage (optional file — replaces the main photo if provided),
 *   galleryImages[] (optional files — added to the gallery, up to the 4-photo cap),
 *   removeGallery (optional JSON array of existing gallery image paths to drop)
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

// ── Read + validate input ───────────────────────────────────────────────────────
$password      = isset($_POST['password'])     ? $_POST['password']                         : '';
$originalName  = isset($_POST['originalName'])  ? trim($_POST['originalName'])                : '';
$name          = isset($_POST['name'])          ? trim(strip_tags($_POST['name']))            : '';
$desc          = isset($_POST['desc'])          ? trim(strip_tags($_POST['desc']))            : '';
$url           = isset($_POST['url'])           ? trim($_POST['url'])                         : '';
$tagsRaw       = isset($_POST['tags'])          ? trim($_POST['tags'])                        : '';
$removeGallery = isset($_POST['removeGallery']) ? json_decode($_POST['removeGallery'], true)  : [];

if (!hash_equals(ADMIN_UPLOAD_PASSWORD, $password)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    exit();
}

if (empty($originalName) || empty($name) || empty($desc) || empty($url)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name, description, and URL are required.']);
    exit();
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid URL.']);
    exit();
}

if (!is_array($removeGallery)) {
    $removeGallery = [];
}

$tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
$tags = array_values($tags);
// ─────────────────────────────────────────────────────────────────────────────

// ── Load + locate the project ───────────────────────────────────────────────────
$dataPath = __DIR__ . '/data/projects.json';

if (!file_exists($dataPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'projects.json not found.']);
    exit();
}

$projects = json_decode(file_get_contents($dataPath), true);
if (!is_array($projects)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'projects.json is invalid.']);
    exit();
}

$index = null;
foreach ($projects as $i => $p) {
    if (isset($p['name']) && $p['name'] === $originalName) {
        $index = $i;
        break;
    }
}

if ($index === null) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Project not found.']);
    exit();
}

// Renaming into a name another project already uses would break edit/delete lookups
if ($name !== $originalName) {
    foreach ($projects as $i => $p) {
        if ($i !== $index && isset($p['name']) && $p['name'] === $name) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Another project already uses that name.']);
            exit();
        }
    }
}

$existing        = $projects[$index];
$existingImages  = isset($existing['images']) && is_array($existing['images']) ? $existing['images'] : [];
$existingMain    = $existingImages[0] ?? null;
$existingGallery = array_slice($existingImages, 1);
// ─────────────────────────────────────────────────────────────────────────────

// ── Photo handling ──────────────────────────────────────────────────────────────
$allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$maxBytes     = 5 * 1024 * 1024;
$uploadsDir   = dirname(__DIR__) . '/demoimage/';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

$safeName = preg_replace('/[^a-z0-9-]/', '', strtolower(str_replace(' ', '-', $name)));

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

function deletePublicImage($publicPath, $uploadsDir) {
    if (!$publicPath) {
        return;
    }
    $full = $uploadsDir . basename($publicPath);
    if (is_file($full)) {
        @unlink($full);
    }
}

// Main photo — replace it if a new file was sent, otherwise keep the current one
$mainPath = $existingMain;
if (isset($_FILES['mainImage']) && $_FILES['mainImage']['error'] === UPLOAD_ERR_OK) {
    $newMain = saveOneImage(
        $_FILES['mainImage']['tmp_name'],
        $_FILES['mainImage']['size'],
        $allowedTypes,
        $maxBytes,
        $uploadsDir,
        $safeName,
        'main'
    );

    if (!$newMain) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Main photo must be JPG, PNG, or WEBP under 5MB.']);
        exit();
    }

    deletePublicImage($existingMain, $uploadsDir);
    $mainPath = $newMain;
}

if (!$mainPath) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'This project needs a main photo.']);
    exit();
}

// Gallery — drop anything checked for removal, keep the rest, then fill remaining
// slots (max 4 total) with newly uploaded photos
$keptGallery = [];
foreach ($existingGallery as $imgPath) {
    if (in_array($imgPath, $removeGallery, true)) {
        deletePublicImage($imgPath, $uploadsDir);
        continue;
    }
    $keptGallery[] = $imgPath;
}

if (isset($_FILES['galleryImages']) && is_array($_FILES['galleryImages']['tmp_name'])) {
    $slotsLeft = max(0, 4 - count($keptGallery));
    $count = count($_FILES['galleryImages']['tmp_name']);

    for ($i = 0; $i < min($count, $slotsLeft); $i++) {
        if ($_FILES['galleryImages']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        $extra = saveOneImage(
            $_FILES['galleryImages']['tmp_name'][$i],
            $_FILES['galleryImages']['size'][$i],
            $allowedTypes,
            $maxBytes,
            $uploadsDir,
            $safeName,
            'gallery-' . ($i + 1)
        );
        if ($extra) {
            $keptGallery[] = $extra;
        }
    }
}

$images = array_values(array_merge([$mainPath], $keptGallery));
// ─────────────────────────────────────────────────────────────────────────────

$projects[$index] = [
    'name'   => $name,
    'desc'   => $desc,
    'url'    => $url,
    'tags'   => $tags,
    'images' => $images,
];

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

echo json_encode(['success' => true, 'message' => 'Project updated.', 'project' => $projects[$index]]);