<?php
//file for session & admin privileges

// Prevent multiple includes
if (!defined('CORE_INCLUDED')) {
    define('CORE_INCLUDED', true);
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    ob_start();

    if (!function_exists('isLoggedIn')) {
        function isLoggedIn() {
            if (!isset($_SESSION['user_id'])) {
                return false;
            }
            return true;
        }
    }

    if (!function_exists('isAdmin')) {
        function isAdmin() {
            if (isLoggedIn()) {
                return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;
            }
            return false;
        }
    }
}
