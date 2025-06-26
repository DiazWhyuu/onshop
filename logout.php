<?php
require_once 'includes/auth.php';

// Hancurkan semua data session
$_SESSION = array();

// Jika ingin menghancurkan session, hapus juga session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header("Location: user/login.php");
exit();
?>