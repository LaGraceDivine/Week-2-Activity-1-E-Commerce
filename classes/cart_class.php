<?php
require_once __DIR__ . '/db_connection.php';

class CartClass extends Database {
    public $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /** Check if product already exists in cart */
    public function check_cart_product($p_id, $c_id) {
        try {
            // For guests (c_id = 0), we should also check by IP address
            if ($c_id == 0) {
                $ip_add = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
                $sql = "SELECT * FROM cart WHERE p_id = :p_id AND c_id = :c_id AND ip_add = :ip_add";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    ':p_id' => $p_id, 
                    ':c_id' => $c_id,
                    ':ip_add' => $ip_add
                ]);
            } else {
                $sql = "SELECT * FROM cart WHERE p_id = :p_id AND c_id = :c_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([':p_id' => $p_id, ':c_id' => $c_id]);
            }
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Check cart product error: " . $e->getMessage());
            return false;
        }
    }

    /** Add item to cart */
    public function add_to_cart($p_id, $c_id, $ip_add, $qty) {
        try {
            // For guests, use IP address in the check
            if ($c_id == 0) {
                $existing = $this->check_cart_product($p_id, $c_id);
            } else {
                $existing = $this->check_cart_product($p_id, $c_id);
            }
            
            if ($existing) {
                // Update quantity instead of duplicating
                $new_qty = intval($existing['qty']) + intval($qty);
                return $this->update_cart_quantity($p_id, $c_id, $new_qty);
            } else {
                // Insert new cart item
                $sql = "INSERT INTO cart (p_id, c_id, ip_add, qty) VALUES (:p_id, :c_id, :ip_add, :qty)";
                $stmt = $this->conn->prepare($sql);
                $result = $stmt->execute([
                    ':p_id' => $p_id,
                    ':c_id' => $c_id,
                    ':ip_add' => $ip_add,
                    ':qty' => $qty
                ]);
                return $result;
            }
        } catch (PDOException $e) {
            error_log("Add to cart error: " . $e->getMessage());
            return false;
        }
    }

    /** Update quantity in cart */
    public function update_cart_quantity($p_id, $c_id, $qty) {
        try {
            $sql = "UPDATE cart SET qty = :qty WHERE p_id = :p_id AND c_id = :c_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':qty' => $qty,
                ':p_id' => $p_id,
                ':c_id' => $c_id
            ]);
        } catch (PDOException $e) {
            error_log("Update cart quantity error: " . $e->getMessage());
            return false;
        }
    }

    /** Remove an item completely from cart */
    public function remove_cart_item($p_id, $c_id) {
        try {
            $sql = "DELETE FROM cart WHERE p_id = :p_id AND c_id = :c_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':p_id' => $p_id,
                ':c_id' => $c_id
            ]);
        } catch (PDOException $e) {
            error_log("Remove cart item error: " . $e->getMessage());
            return false;
        }
    }

    /** Get all cart items for a user */
    public function get_user_cart($c_id) {
        try {
            $sql = "SELECT c.*, p.product_title, p.product_price, p.product_image, p.product_id
                    FROM cart c 
                    JOIN products p ON c.p_id = p.product_id
                    WHERE c.c_id = :c_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':c_id' => $c_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user cart error: " . $e->getMessage());
            return [];
        }
    }

    /** Empty the entire cart for a user */
    public function empty_cart($c_id) {
        try {
            $sql = "DELETE FROM cart WHERE c_id = :c_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':c_id' => $c_id]);
        } catch (PDOException $e) {
            error_log("Empty cart error: " . $e->getMessage());
            return false;
        }
    }
}