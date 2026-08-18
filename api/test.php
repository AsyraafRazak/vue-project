<?php
/**
 * DEBUG FILE — delete after testing, do NOT keep on production.
 * Visit: yourdomain.com/api/test.php
 * Checks if config.php and PHPMailer are found correctly.
 */

$results = [];

// Check config.php paths
$localPath     = dirname(__DIR__, 2) . '/config.php';
$hostingerPath = dirname(__DIR__, 3) . '/config.php';

$results['config_local_path']       = $localPath;
$results['config_local_exists']     = file_exists($localPath) ? 'YES ✓' : 'NO ✗';
$results['config_hostinger_path']   = $hostingerPath;
$results['config_hostinger_exists'] = file_exists($hostingerPath) ? 'YES ✓' : 'NO ✗';

// Check PHPMailer
$phpmailerPath = __DIR__ . '/PHPMailer/src/PHPMailer.php';
$results['phpmailer_path']   = $phpmailerPath;
$results['phpmailer_exists'] = file_exists($phpmailerPath) ? 'YES ✓' : 'NO ✗';

header('Content-Type: application/json');
echo json_encode($results, JSON_PRETTY_PRINT);
