<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Session destroy karne se pehle user check karne ki zaroorat nahi hai
// Sirf session destroy karo

session_unset();
session_destroy();

// Session cookie bhi clear karo
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

header("Location: login.html");
exit();
?>