<?php
// Prevent multiple includes
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/../settings/core.php';
}

// Alias functions for consistency (some files use isloggedIn vs isLoggedIn)
if (!function_exists('isloggedIn')) {
    function isloggedIn() {
        return isLoggedIn();
    }
}

