<?php
/**
 * Projects List API
 * Returns the current demo/project list as JSON.
 *
 * DemoPage.vue fetches this on mount instead of using a hardcoded array.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$dataPath = __DIR__ . '/data/projects.json';

if (!file_exists($dataPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'projects.json not found.']);
    exit();
}

$raw = file_get_contents($dataPath);
$projects = json_decode($raw, true);

if ($projects === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'projects.json is invalid.']);
    exit();
}

echo json_encode($projects);