<?php
session_start();
require_once "../controllers/customer_controller.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        header("Location: ../login.php?error=Please fill all fields");
        exit;
    }

    $result = login_customer_ctr($email, $password);

    if ($result && is_array($result)) {

        $_SESSION['user_id'] = $result['id'];
        $_SESSION['user_name'] = $result['full_name'];
        $_SESSION['user_role'] = $result['user_role'];

        header("Location: ../login/dashboard.php?success=Welcome " . urlencode($result['full_name']));
        exit;
    } else {
        header("Location: ../login/login.php?error=Invalid email or password");
        exit;
    }
} else {
    header("Location: ../login/login.php?error=Invalid request");
    exit;
}
