<?php
require_once dirname(__FILE__) . '/db_connection.php';
require_once '../classes/brand_class.php';

class Brand extends Database {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Add brand
    public function add_brand($brand_name, $category_id, $user_id) {
        $sql = "INSERT INTO brands (brand_name, category_id, user_id)
                VALUES (:brand_name, :category_id, :user_id)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':brand_name' => $brand_name,
            ':category_id' => $category_id,
            ':user_id' => $user_id
        ]);
    }

    // Get all brands by user
    public function get_brands_by_user($user_id) {
        $sql = "SELECT b.brand_id, b.brand_name, c.name AS category_name
                FROM brands b
                JOIN categories c ON b.category_id = c.id
                WHERE b.user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update brand
    public function update_brand($brand_id, $brand_name) {
        $sql = "UPDATE brands SET brand_name = :brand_name WHERE brand_id = :brand_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':brand_name' => $brand_name,
            ':brand_id' => $brand_id
        ]);
    }

    // Delete brand
    public function delete_brand($brand_id) {
        $sql = "DELETE FROM brands WHERE brand_id = :brand_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':brand_id' => $brand_id]);
    }
}
