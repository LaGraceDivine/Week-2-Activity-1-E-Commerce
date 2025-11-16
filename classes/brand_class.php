<?php
require_once __DIR__ . '/../classes/db_connection.php';

class Brand extends Database {

    // Add brand
    public function add_brand($brand_name, $category_id, $user_id) {
        $conn = $this->connect();
        $sql = "INSERT INTO brands (brand_name, category_id, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Brand insert prepare failed: " . print_r($conn->errorInfo(), true));
            return false;
        }
        $result = $stmt->execute([$brand_name, $category_id, $user_id]);
        if (!$result) {
            error_log("Brand insert execute failed: " . print_r($stmt->errorInfo(), true));
            return false;
        }
        return $result;
    }

    // Get all brands by user
    public function get_brands_by_user($user_id) {
        $conn = $this->connect();
        $sql = "SELECT b.brand_id, b.brand_name, b.category_id, c.name AS category_name 
                FROM brands b 
                JOIN categories c ON b.category_id = c.id 
                WHERE b.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update brand
    public function update_brand($brand_id, $brand_name) {
        $conn = $this->connect();
        $sql = "UPDATE brands SET brand_name = ? WHERE brand_id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$brand_name, $brand_id]);
    }

    // Delete brand
    public function delete_brand($brand_id) {
        $conn = $this->connect();
        $sql = "DELETE FROM brands WHERE brand_id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$brand_id]);
    }
}
?>
