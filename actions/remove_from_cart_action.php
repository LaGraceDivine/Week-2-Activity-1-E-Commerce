<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once(__DIR__ . '/../controllers/cart_controller.php');

$p_id = intval($_POST['product_id'] ?? $_POST['p_id'] ?? 0);
$c_id = $_SESSION['user_id'] ?? 0;

if (!$p_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

try {
    $controller = new CartController();
    $result = $controller->remove_from_cart_ctr($p_id, $c_id);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
    }
} catch (Exception $e) {
    error_log("Remove from cart action error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}