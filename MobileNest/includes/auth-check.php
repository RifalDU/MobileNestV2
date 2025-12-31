<?php
// ULTRA MINIMAL AUTH-CHECK - NO FANCY FEATURES

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BASIC FUNCTION ONLY
function require_user_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: ' . SITE_URL . '/user/login.php');
        exit;
    }
}

function require_admin_login() {
    if (!isset($_SESSION['admin'])) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

function is_user_logged_in() {
    return isset($_SESSION['user']);
}

function is_admin_logged_in() {
    return isset($_SESSION['admin']);
}

function get_user_id() {
    return $_SESSION['user'] ?? null;
}

function get_admin_id() {
    return $_SESSION['admin'] ?? null;
}
?>