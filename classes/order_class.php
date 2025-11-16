<?php
require_once __DIR__ . '/db_connection.php';

class OrderClass extends Database {
    public $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /** Create a new order and return the order ID */
    public function create_order($customer_id, $invoice_no, $order_status) {
        try {
            $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status)
                    VALUES (:customer_id, :invoice_no, NOW(), :order_status)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':customer_id' => $customer_id,
                ':invoice_no' => $invoice_no,
                ':order_status' => $order_status
            ]);
            
            if ($result) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Create order error: " . $e->getMessage());
            return false;
        }
    }

    /** Get the last inserted order ID */
    public function get_last_order_id() {
        try {
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Get last order ID error: " . $e->getMessage());
            return false;
        }
    }

    /** Add order details (product ID, quantity, price) to orderdetails table */
    public function add_order_detail($order_id, $product_id, $qty) {
        try {
            // First get the product price
            $price_sql = "SELECT product_price FROM products WHERE product_id = :product_id";
            $price_stmt = $this->conn->prepare($price_sql);
            $price_stmt->execute([':product_id' => $product_id]);
            $product = $price_stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                error_log("Product not found: " . $product_id);
                return false;
            }
            
            $price = $product['product_price'];
            
            $sql = "INSERT INTO orderdetails (order_id, product_id, qty, price)
                    VALUES (:order_id, :product_id, :qty, :price)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $product_id,
                ':qty' => $qty,
                ':price' => $price
            ]);
        } catch (PDOException $e) {
            error_log("Add order detail error: " . $e->getMessage());
            return false;
        }
    }

    /** Record payment in the payments table */
    public function record_payment($amt, $customer_id, $order_id, $currency) {
        try {
            $sql = "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date)
                    VALUES (:amt, :customer_id, :order_id, :currency, NOW())";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':amt' => $amt,
                ':customer_id' => $customer_id,
                ':order_id' => $order_id,
                ':currency' => $currency
            ]);
        } catch (PDOException $e) {
            error_log("Record payment error: " . $e->getMessage());
            return false;
        }
    }

    /** Retrieve past orders for a user */
    public function get_customer_orders($customer_id) {
        try {
            $sql = "SELECT o.*, 
                    (SELECT SUM(od.qty * od.price) FROM orderdetails od WHERE od.order_id = o.order_id) as total_amount
                    FROM orders o
                    WHERE o.customer_id = :customer_id
                    ORDER BY o.order_date DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':customer_id' => $customer_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get customer orders error: " . $e->getMessage());
            return [];
        }
    }

    /** Get order details for a specific order */
    public function get_order_details($order_id) {
        try {
            $sql = "SELECT od.*, p.product_title, p.product_image
                    FROM orderdetails od
                    JOIN products p ON od.product_id = p.product_id
                    WHERE od.order_id = :order_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':order_id' => $order_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get order details error: " . $e->getMessage());
            return [];
        }
    }
}