<?php
require_once dirname(__FILE__).'/../settings/db_class.php';

class Brand extends Database {

    // Add brand
    public function add_brand($brand_name, $category_id, $user_id) {
        $conn = $this->connect();
        $sql = "INSERT INTO brands (brand_name, category_id, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            print_r($conn->errorInfo());
            return false;
        }
        $result = $stmt->execute([$brand_name, $category_id, $user_id]);
        if (!$result) {
            print_r($stmt->errorInfo());
        }
        return $result;
    }

    // Get all brands by user
    public function get_brands_by_user($user_id) {
        $conn = $this->connect();
        $sql = "SELECT b.brand_id, b.brand_name, c.name AS category_name 
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
