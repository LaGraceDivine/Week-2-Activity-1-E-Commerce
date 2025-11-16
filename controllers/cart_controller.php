<?php
require_once __DIR__ . '/../classes/cart_class.php';

class CartController {
    private $cart;

    public function __construct() {
        $this->cart = new CartClass();
    }

    /** Add item to cart */
    public function add_to_cart_ctr($p_id, $c_id, $ip_add, $qty) {
        return $this->cart->add_to_cart($p_id, $c_id, $ip_add, $qty);
    }

    /** Update quantity in cart */
    public function update_cart_item_ctr($p_id, $c_id, $qty) {
        return $this->cart->update_cart_quantity($p_id, $c_id, $qty);
    }

    /** Remove item from cart */
    public function remove_from_cart_ctr($p_id, $c_id) {
        return $this->cart->remove_cart_item($p_id, $c_id);
    }

    /** Get user cart items */
    public function get_user_cart_ctr($c_id) {
        return $this->cart->get_user_cart($c_id);
    }

    /** Empty cart */
    public function empty_cart_ctr($c_id) {
        return $this->cart->empty_cart($c_id);
    }

    /** Check if product exists in cart */
    public function check_existing_cart_ctr($p_id, $c_id) {
        return $this->cart->check_cart_product($p_id, $c_id);
    }
}

// Legacy function wrappers for backward compatibility
function add_to_cart_ctr($p_id, $c_id, $ip_add, $qty) {
    $controller = new CartController();
    return $controller->add_to_cart_ctr($p_id, $c_id, $ip_add, $qty);
}

function update_quantity_ctr($p_id, $c_id, $qty) {
    $controller = new CartController();
    return $controller->update_cart_item_ctr($p_id, $c_id, $qty);
}

function remove_from_cart_ctr($p_id, $c_id) {
    $controller = new CartController();
    return $controller->remove_from_cart_ctr($p_id, $c_id);
}

function get_user_cart_ctr($c_id) {
    $controller = new CartController();
    return $controller->get_user_cart_ctr($c_id);
}

function empty_cart_ctr($c_id) {
    $controller = new CartController();
    return $controller->empty_cart_ctr($c_id);
}

function check_existing_cart_ctr($p_id, $c_id) {
    $controller = new CartController();
    return $controller->check_existing_cart_ctr($p_id, $c_id);
}