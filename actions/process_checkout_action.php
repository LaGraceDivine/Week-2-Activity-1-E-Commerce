<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once(__DIR__ . '/../controllers/cart_controller.php');
require_once(__DIR__ . '/../controllers/order_controller.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Please login to proceed with checkout'
    ]);
    exit;
}

$c_id = $_SESSION['user_id'];
$currency = "GHS";

try {
    // 1. Get cart items
    $cart_controller = new CartController();
    $cart_items = $cart_controller->get_user_cart_ctr($c_id);
    
    if (!$cart_items || count($cart_items) === 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Your cart is empty'
        ]);
        exit;
    }
    
    // 2. Generate unique invoice number
    $invoice_no = 'INV-' . date('Ymd') . '-' . rand(100000, 999999);
    
    // 3. Create order
    $order_controller = new OrderController();
    $order_id = $order_controller->create_order_ctr($c_id, $invoice_no, 'Completed');
    
    if (!$order_id) {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to create order'
        ]);
        exit;
    }
    
    // 4. Insert each cart item into orderdetails table and calculate total
    $amount = 0;
    $item_count = 0;
    
    foreach ($cart_items as $item) {
        $product_id = $item['p_id'] ?? $item['product_id'] ?? 0;
        $qty = intval($item['qty']);
        $price = floatval($item['product_price'] ?? 0);
        
        if ($product_id > 0) {
            $detail_result = $order_controller->add_order_details_ctr($order_id, $product_id, $qty);
            if ($detail_result) {
                $amount += ($price * $qty);
                $item_count += $qty;
            }
        }
    }
    
    // 5. Record payment
    $payment_result = $order_controller->record_payment_ctr($amount, $c_id, $order_id, $currency);
    
    if (!$payment_result) {
        error_log("Payment recording failed for order: $order_id");
        // Continue anyway as order is created
    }
    
    // 6. Empty cart
    $empty_result = $cart_controller->empty_cart_ctr($c_id);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'invoice' => $invoice_no,
        'amount' => number_format($amount, 2),
        'item_count' => $item_count,
        'currency' => $currency,
        'message' => 'Order processed successfully!'
    ]);
    
} catch (Exception $e) {
    error_log("Process checkout error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during checkout: ' . $e->getMessage()
    ]);
}