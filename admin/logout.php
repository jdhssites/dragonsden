<?php
session_start();

// Clear session variables
$_SESSION = array();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Clear remember me cookies if they exist
if(isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}
if(isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to home page
header('Location: index.php');
exit;

