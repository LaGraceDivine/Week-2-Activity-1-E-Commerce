<?php
require_once __DIR__ . '/../classes/order_class.php';

class OrderController {
    private $order;

    public function __construct() {
        $this->order = new OrderClass();
    }

    /** Create a new order */
    public function create_order_ctr($customer_id, $invoice_no, $order_status) {
        return $this->order->create_order($customer_id, $invoice_no, $order_status);
    }

    /** Get last order ID */
    public function get_last_order_id_ctr() {
        return $this->order->get_last_order_id();
    }

    /** Add order details */
    public function add_order_details_ctr($order_id, $product_id, $qty) {
        return $this->order->add_order_detail($order_id, $product_id, $qty);
    }

    /** Record payment */
    public function record_payment_ctr($amt, $customer_id, $order_id, $currency) {
        return $this->order->record_payment($amt, $customer_id, $order_id, $currency);
    }

    /** Get customer orders */
    public function get_customer_orders_ctr($customer_id) {
        return $this->order->get_customer_orders($customer_id);
    }

    /** Get order details */
    public function get_order_details_ctr($order_id) {
        return $this->order->get_order_details($order_id);
    }
}

// Legacy function wrappers for backward compatibility
function create_order_ctr($customer_id, $invoice_no, $order_status) {
    $controller = new OrderController();
    return $controller->create_order_ctr($customer_id, $invoice_no, $order_status);
}

function get_last_order_id_ctr() {
    $controller = new OrderController();
    return $controller->get_last_order_id_ctr();
}

function add_order_details_ctr($order_id, $product_id, $qty) {
    $controller = new OrderController();
    return $controller->add_order_details_ctr($order_id, $product_id, $qty);
}

function record_payment_ctr($amt, $customer_id, $order_id, $currency) {
    $controller = new OrderController();
    return $controller->record_payment_ctr($amt, $customer_id, $order_id, $currency);
}

function get_customer_orders_ctr($customer_id) {
    $controller = new OrderController();
    return $controller->get_customer_orders_ctr($customer_id);
}

function get_order_details_ctr($order_id) {
    $controller = new OrderController();
    return $controller->get_order_details_ctr($order_id);
}
