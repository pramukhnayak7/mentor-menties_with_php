<?php
session_start();

// Destroy all session variables
$_SESSION = [];

// Destroy the session cookie (optional but better for full logout)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

// Redirect to login page (or homepage)
header("Location: index.html");
exit();
?>
