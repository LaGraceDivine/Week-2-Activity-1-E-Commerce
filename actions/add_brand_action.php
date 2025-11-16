<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../controllers/brand_controller.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if (isset($_POST['brand_name'], $_POST['category_id'])) {
    $user_id = $_SESSION['user_id'];
    $brand_name = trim($_POST['brand_name']);
    $category_id = intval($_POST['category_id']);

    // Validate inputs
    if (empty($brand_name)) {
        echo json_encode(['success' => false, 'message' => 'Brand name cannot be empty']);
        exit;
    }
    
    if ($category_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Please select a valid category']);
        exit;
    }

    try {
        $result = add_brand_ctr($brand_name, $category_id, $user_id);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Brand added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add brand. Please check the server logs for details.']);
        }
    } catch (Exception $e) {
        error_log("Brand add error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    $missing = [];
    if (!isset($_POST['brand_name'])) $missing[] = 'brand_name';
    if (!isset($_POST['category_id'])) $missing[] = 'category_id';
    echo json_encode(['success' => false, 'message' => 'Missing required fields: ' . implode(', ', $missing)]);
}
?>
