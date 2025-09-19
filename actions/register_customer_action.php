<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once "../controllers/customer_controller.php";

// Helper function for JSON response
function respond($status, $message) {
    echo json_encode(["status" => $status, "message" => $message]);
    exit();
}

// Capture POST data safely
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$country = trim($_POST['country'] ?? '');
$city = trim($_POST['city'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');

// Simple validation
if (!$full_name || !$email || !$password || !$country || !$city || !$contact_number) {
    respond("error", "All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond("error", "Invalid email address.");
}

if (!preg_match('/^[0-9\-\+\s\(\)]+$/', $contact_number)) {
    respond("error", "Invalid phone number.");
}

// Prepare data array for controller
$customer_data = [
    "full_name" => $full_name,
    "email" => $email,
    "password" => $password,  // raw password
    "country" => $country,
    "city" => $city,
    "contact_number" => $contact_number
];

// Try to register customer
try {
    $result = register_customer_ctr($customer_data);

    if ($result === "success") {
        respond("success", "Registration successful");
    } else {
        respond("error", $result ?: "Unknown error during registration");
    }

} catch (Exception $e) {
    respond("error", "Exception occurred: " . $e->getMessage());
}
