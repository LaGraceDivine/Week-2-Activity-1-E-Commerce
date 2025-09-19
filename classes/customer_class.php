<?php
require_once "db_connection.php";

class Customer extends Database {

    public function addCustomer($full_name, $email, $password, $country, $city, $contact_number, $user_role = 2) {
        try {
            $conn = $this->connect();

            // Checking if email exists
            $stmt = $conn->prepare("SELECT id FROM customers WHERE LOWER(email) = LOWER(?)");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                file_put_contents('debug.txt', "Email already exists: $email\n", FILE_APPEND);
                return "Email already exists";
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare(
                "INSERT INTO customers (full_name, email, password, country, city, contact_number, user_role)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            if ($stmt->execute([$full_name, $email, $hashedPassword, $country, $city, $contact_number, $user_role])) {
                file_put_contents('debug.txt', "Inserted: $full_name, $email\n", FILE_APPEND);
                return "success";
            } else {
                $error = $stmt->errorInfo()[2];
                file_put_contents('debug.txt', "Insert failed: $error\n", FILE_APPEND);
                return "Error creating account: $error";
            }

        } catch (Exception $e) {
            file_put_contents('debug.txt', "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            return "Exception: " . $e->getMessage();
        }
    }
}
