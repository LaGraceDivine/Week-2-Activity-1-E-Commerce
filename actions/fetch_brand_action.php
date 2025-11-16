<?php
header('Content-Type: application/json; charset=utf-8');
require_once "../controllers/brand_controller.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$brands = get_brands_by_user_ctr($user_id);

echo json_encode([
    'success' => true,
    'brands' => $brands
]);

