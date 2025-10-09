<?php
require_once("../settings/core.php");
require_once "../controllers/category_controller.php";

if (!isLoggedIn() || !isAdmin()) {
    echo "Unauthorized";
    exit;
}

if (isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $user_id = $_SESSION['user_id'];
    echo add_category_ctr($name, $user_id);
} else {
    echo "No category name provided.";
}
