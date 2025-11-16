<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once(__DIR__ . '/../controllers/cart_controller.php');

$c_id = $_SESSION['user_id'] ?? 0;

try {
    $controller = new CartController();
    $result = $controller->empty_cart_ctr($c_id);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Cart emptied successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to empty cart']);
    }
} catch (Exception $e) {
    error_log("Empty cart action error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}