<?php
require_once "../core.php";
require_once "../controllers/category_controller.php";

if (!isLoggedIn() || !isAdmin()) {
    echo "Unauthorized";
    exit;
}

if (isset($_POST['id'], $_POST['name'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $user_id = $_SESSION['user_id'];
    echo update_category_ctr($id, $name, $user_id);
} else {
    echo "Missing fields.";
}
