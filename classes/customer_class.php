<?php
require_once "db_connection.php";

class Customer extends Database {

    public function addCustomer($full_name, $email, $password, $country, $city, $contact_number, $user_role = 2) {
        try {
            $conn = $this->connect();

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
    public function getCustomerByEmail($email) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            file_put_contents('debug.txt', "getCustomerByEmail Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            return null;
        }
    }

    public function loginCustomer($email, $password) {
        try {
            $customer = $this->getCustomerByEmail($email);

            if ($customer && password_verify($password, $customer['password'])) {
                return $customer;
            }
            return false;
        } catch (Exception $e) {
            file_put_contents('debug.txt', "loginCustomer Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
}
