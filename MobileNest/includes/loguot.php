<?php
/**
 * Logout Handler
 * Destroys session and redirects to login page
 * 
 * Location: includes/logout.php
 * Redirect to: user/login.php?logged_out=true
 * 
 * Usage: Click logout link that points to this file
 * <a href="<?php echo SITE_URL; ?>/includes/logout.php">Logout</a>
 */

session_start();
require_once dirname(__DIR__) . '/config.php';

// Destroy all session variables
if (isset($_SESSION)) {
    $_SESSION = array();
}
session_destroy();

// Clear session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Redirect to login page with success message
header('Location: ' . SITE_URL . '/user/login.php?logged_out=true');
exit();

?>