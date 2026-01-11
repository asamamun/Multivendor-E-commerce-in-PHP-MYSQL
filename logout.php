<?php
session_start();

// Destroy session
session_unset();
session_destroy();

// Delete cookies (must match original path!)
$cookies = ['user_id', 'user_name', 'user_email', 'user_role'];

foreach ($cookies as $cookie) {
    if (isset($_COOKIE[$cookie])) {
        setcookie($cookie, '', time() - 3600, '/');
        unset($_COOKIE[$cookie]);
    }
}
// Redirect
header("Location: index.php");
exit;
