<?php
require_once "../core.php";
require_once "../controllers/category_controller.php";

if (!isLoggedIn() || !isAdmin()) {
    echo "Unauthorized";
    exit;
}

$user_id = $_SESSION['user_id'];
$categories = get_categories_ctr($user_id);
echo json_encode($categories);
