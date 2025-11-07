<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../classes/db_connection.php';
require_once __DIR__ . '/../settings/session_check.php'; // optional, if you track session auth here

// Basic auth check (still works like your previous setup)
session_start();
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$uid = $_SESSION['user_id'];

// Define upload base directory
$base = realpath(__DIR__ . '/../uploads');
if ($base === false) {
    echo json_encode(['success' => false, 'error' => 'Uploads folder missing']);
    exit;
}

// Check file upload presence
if (!isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'error' => 'No file']);
    exit;
}

$file = $_FILES['image'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Upload error']);
    exit;
}

// Validate file size (max 5MB)
$maxBytes = 5 * 1024 * 1024;
if ($file['size'] > $maxBytes) {
    echo json_encode(['success' => false, 'error' => 'File too large']);
    exit;
}

// Validate MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowed = [
    'image/png' => 'png',
    'image/jpeg' => 'jpg',
    'image/webp' => 'webp'
];

if (!isset($allowed[$mime])) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type']);
    exit;
}

$ext = $allowed[$mime];

// Handle product directory structure
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$uDir = $base . DIRECTORY_SEPARATOR . "u{$uid}";
if (!is_dir($uDir)) mkdir($uDir, 0755, true);

$pDir = $product_id ? $uDir . DIRECTORY_SEPARATOR . "p{$product_id}" : $uDir . DIRECTORY_SEPARATOR . "temp";
if (!is_dir($pDir)) mkdir($pDir, 0755, true);

// Securely generate file name
$filename = 'image_' . time() . '_' . substr(bin2hex(random_bytes(6)), 0, 12) . '.' . $ext;
$destination = $pDir . DIRECTORY_SEPARATOR . $filename;

// Prevent directory traversal
$realDestDir = realpath(dirname($destination));
if (strpos($realDestDir, $base) !== 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid destination']);
    exit;
}

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    echo json_encode(['success' => false, 'error' => 'Move failed']);
    exit;
}

// Build relative file path for DB/storage reference
$relativePath = 'uploads/u' . $uid . ($product_id ? "/p{$product_id}/" : "/temp/") . $filename;
echo json_encode(['success' => true, 'path' => $relativePath]);
