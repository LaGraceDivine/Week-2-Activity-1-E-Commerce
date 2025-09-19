<?php
class Database {
    private $host = "localhost";
    private $db_name = "ecommerce_db";
    private $username = "root";
    private $password = "root";
    public $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // Return error instead of die() for proper fetch handling
            throw new Exception("Database connection error: " . $e->getMessage());
        }

        return $this->conn;
    }
}
