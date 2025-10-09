<?php
require_once("../settings/core.php");
require_once "../controllers/category_controller.php";

if (!isLoggedIn() || !isAdmin()) {
    echo "Unauthorized";
    exit;
}

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $user_id = $_SESSION['user_id'];
    echo delete_category_ctr($id, $user_id);
} else {
    echo "Missing ID.";
}
