<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../controllers/brand_controller.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if (isset($_POST['brand_name']) && isset($_POST['category_id'])) {
    $user_id = $_SESSION['user_id'];
    $brand_name = trim($_POST['brand_name']);
    $category_id = intval($_POST['category_id']);

    $result = add_brand_ctr($brand_name, $category_id, $user_id);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Brand added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add brand']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing brand name or category']);
}
