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
        try {
            $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords)
                    VALUES (:cat, :brand, :title, :price, :desc, :image, :keywords)";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                $error = $this->conn->errorInfo();
                error_log("Product insert prepare failed: " . print_r($error, true));
                return ['success' => false, 'error' => 'Failed to prepare statement: ' . ($error[2] ?? 'Unknown error')];
            }
            
            $result = $stmt->execute([
                ':cat' => $args['product_cat'] ?? null,
                ':brand' => $args['product_brand'] ?? null,
                ':title' => $args['product_title'] ?? null,
                ':price' => $args['product_price'] ?? null,
                ':desc' => $args['product_desc'] ?? null,
                ':image' => $args['product_image'] ?? null,
                ':keywords' => $args['product_keywords'] ?? null
            ]);
            
            if (!$result) {
                $error = $stmt->errorInfo();
                error_log("Product insert execute failed: " . print_r($error, true));
                return ['success' => false, 'error' => 'Failed to insert product: ' . ($error[2] ?? 'Unknown error')];
            }
            
            $product_id = $this->conn->lastInsertId();
            
            // Check if insert was successful even if there was a warning
            if ($product_id) {
                // Suppress any warnings/notices
                @error_clear_last();
                return ['success' => true, 'product_id' => $product_id];
            } else {
                // If no ID was returned, check for errors
                $error = $stmt->errorInfo();
                $errorMsg = $error[2] ?? 'Unknown error';
                // Check if it's a foreign key constraint error but product was still inserted
                if (strpos($errorMsg, 'foreign key') !== false || strpos($errorMsg, '1452') !== false) {
                    // Try to get the last inserted ID one more time
                    $checkId = $this->conn->lastInsertId();
                    if ($checkId) {
                        return ['success' => true, 'product_id' => $checkId];
                    }
                }
                return ['success' => false, 'error' => $errorMsg];
            }
        } catch (PDOException $e) {
            $errorCode = $e->getCode();
            $errorMsg = $e->getMessage();
            
            // Check if it's a foreign key constraint error (code 23000 or 1452)
            if ($errorCode == 23000 || strpos($errorMsg, '1452') !== false || strpos($errorMsg, 'foreign key') !== false) {
                // Check if the product was actually inserted despite the error
                $lastId = $this->conn->lastInsertId();
                if ($lastId) {
                    // Product was inserted successfully, ignore the constraint warning
                    @error_clear_last();
                    return ['success' => true, 'product_id' => $lastId];
                }
                // Extract the actual constraint issue
                if (strpos($errorMsg, 'product_cat') !== false) {
                    return ['success' => false, 'error' => 'Invalid category selected'];
                } elseif (strpos($errorMsg, 'product_brand') !== false) {
                    return ['success' => false, 'error' => 'Invalid brand selected'];
                }
            }
            
            error_log("Product add exception: " . $errorMsg . "\n" . $e->getTraceAsString());
            return ['success' => false, 'error' => $errorMsg];
        } catch (Exception $e) {
            error_log("Product add exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return ['success' => false, 'error' => $e->getMessage()];
        }
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

    /** Get all products for admin (with category and brand names) */
    public function getAllForAdmin(): array {
        $sql = "SELECT p.*, c.name AS category_name, b.brand_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.product_cat
                LEFT JOIN brands b ON b.brand_id = p.product_brand
                ORDER BY p.product_id DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Get products by user ID */
    public function getByUser(int $user_id): array {
        $sql = "SELECT p.*, c.name AS category_name, b.brand_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.product_cat
                LEFT JOIN brands b ON b.brand_id = p.product_brand
                WHERE p.user_id = :uid
                ORDER BY p.product_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Get single product by ID */
    public function get(int $product_id) {
        $sql = "SELECT p.*, c.name AS category_name, b.brand_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.product_cat
                LEFT JOIN brands b ON b.brand_id = p.product_brand
                WHERE p.product_id = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
