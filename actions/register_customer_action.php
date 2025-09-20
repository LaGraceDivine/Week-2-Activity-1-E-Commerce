<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once "../controllers/customer_controller.php";


function respond($status, $message) {
    echo json_encode(["status" => $status, "message" => $message]);
    exit();
}

$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$country = trim($_POST['country'] ?? '');
$city = trim($_POST['city'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');

//validation
if (!$full_name || !$email || !$password || !$country || !$city || !$contact_number) {
    respond("error", "All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond("error", "Invalid email address.");
}

if (!preg_match('/^[0-9\-\+\s\(\)]+$/', $contact_number)) {
    respond("error", "Invalid phone number.");
}

$customer_data = [
    "full_name" => $full_name,
    "email" => $email,
    "password" => $password,
    "country" => $country,
    "city" => $city,
    "contact_number" => $contact_number
];

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
