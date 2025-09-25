<?php
//file for session & admin privileges

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

function isLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    return true;
}

function isAdmin() {
    if (isLoggedIn()) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;
    }
    return false;
}
