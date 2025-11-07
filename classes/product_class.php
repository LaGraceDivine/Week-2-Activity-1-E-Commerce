<?php
require_once __DIR__ . '/../classes/db_connection.php';

class ProductClass extends Database {
    public $conn;

    public function __construct() {
        // connect using the shared Database class
        $database = new Database();
        $this->conn = $database->connect();
    }

    /** Add a new product */
    public function add(array $args): array {
        $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords, user_id)
                VALUES (:cat, :brand, :title, :price, :desc, :image, :keywords, :uid)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':cat' => $args['product_cat'],
            ':brand' => $args['product_brand'],
            ':title' => $args['product_title'],
            ':price' => $args['product_price'],
            ':desc' => $args['product_desc'],
            ':image' => $args['product_image'] ?? null,
            ':keywords' => $args['product_keywords'] ?? null,
            ':uid' => $args['user_id'] ?? null
        ]);
        return ['success' => true, 'product_id' => $this->conn->lastInsertId()];
    }

    /** Update a product */
    public function update(int $product_id, array $args): array {
        $sql = "UPDATE products SET 
                    product_cat = :cat,
                    product_brand = :brand,
                    product_title = :title,
                    product_price = :price,
                    product_desc = :desc,
                    product_image = :image,
                    product_keywords = :keywords
                WHERE product_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':cat' => $args['product_cat'],
            ':brand' => $args['product_brand'],
            ':title' => $args['product_title'],
            ':price' => $args['product_price'],
            ':desc' => $args['product_desc'],
            ':image' => $args['product_image'] ?? null,
            ':keywords' => $args['product_keywords'] ?? null,
            ':id' => $product_id
        ]);
        return ['success' => true, 'rows' => $stmt->rowCount()];
    }

    /** Customer-facing methods */

    // Get all products for the storefront
    public function view_all_products(): array {
        $sql = "SELECT p.*, c.name AS category_name, b.brand_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.product_cat
                LEFT JOIN brands b ON b.brand_id = p.product_brand
                ORDER BY p.product_id DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single product details
    public function view_single_product(int $id) {
        $sql = "SELECT p.*, c.name AS category_name, b.brand_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.product_cat
                LEFT JOIN brands b ON b.brand_id = p.product_brand
                WHERE p.product_id = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Search products by title or keywords
    public function search_products(string $query): array {
        $sql = "SELECT p.*, c.name AS category_name, b.brand_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.product_cat
                LEFT JOIN brands b ON b.brand_id = p.product_brand
                WHERE p.product_title LIKE :query OR p.product_keywords LIKE :query
                ORDER BY p.product_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':query' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Filter products by category
    public function filter_products_by_category(int $cat_id): array {
        $sql = "SELECT p.*, c.name AS category_name, b.brand_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.product_cat
                LEFT JOIN brands b ON b.brand_id = p.product_brand
                WHERE p.product_cat = :cat_id
                ORDER BY p.product_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':cat_id' => $cat_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Filter products by brand
    public function filter_products_by_brand(int $brand_id): array {
        $sql = "SELECT p.*, c.name AS category_name, b.brand_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.product_cat
                LEFT JOIN brands b ON b.brand_id = p.product_brand
                WHERE p.product_brand = :brand_id
                ORDER BY p.product_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':brand_id' => $brand_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
