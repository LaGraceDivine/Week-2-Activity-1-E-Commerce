<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once(__DIR__ . '/../controllers/cart_controller.php');

$p_id = intval($_POST['product_id'] ?? $_POST['p_id'] ?? 0);
$qty = max(1, intval($_POST['qty'] ?? 1));
$c_id = $_SESSION['user_id'] ?? 0;

if (!$p_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

try {
    $controller = new CartController();
    $result = $controller->update_cart_item_ctr($p_id, $c_id, $qty);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Quantity updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update quantity']);
    }
} catch (Exception $e) {
    error_log("Update quantity action error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}