<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/brand.php';
require_once __DIR__ . '/../config/session.php'; // if you have a session file
require_once __DIR__ . '/../classes/brand_class.php';

session_start();

// Check user authentication and role
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = (int)($data['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    exit;
}

$brand = new Brand();
$deleted = $brand->delete_brand($id);

if ($deleted) {
    echo json_encode(['success' => true, 'message' => 'Brand deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to delete brand.']);
}
