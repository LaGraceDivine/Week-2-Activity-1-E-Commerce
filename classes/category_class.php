<?php
require_once "db_connection.php";

class Category extends Database {

    public function addCategory($name, $user_id) {
        try {
            $conn = $this->connect();

            //checking if the category exists
            $stmt = $conn->prepare("SELECT id FROM categories WHERE LOWER(name) = LOWER(?) AND user_id = ?");
            $stmt->execute([$name, $user_id]);
            if ($stmt->rowCount() > 0) {
                return "Category already exists!";
            }

            $stmt = $conn->prepare("INSERT INTO categories (name, user_id) VALUES (?, ?)");
            if ($stmt->execute([$name, $user_id])) {
                return "success";
            }
            return "Failed to add category.";

        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getCategoriesByUser($user_id) {
        $conn = $this->connect();
        $stmt = $conn->prepare("SELECT * FROM categories WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateCategory($id, $newName, $user_id) {
        $conn = $this->connect();
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$newName, $id, $user_id])) {
            return "success";
        }
        return "Update failed.";
    }

    public function deleteCategory($id, $user_id) {
        $conn = $this->connect();
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$id, $user_id])) {
            return "success";
        }
        return "Delete failed.";
    }
}
