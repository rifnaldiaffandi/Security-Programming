<?php
session_start();

// Hapus semua session
$_SESSION = array();
session_destroy();

// Hapus cookie "Remember Me"
if (isset($_COOKIE['remember'])) {
    unset($_COOKIE['remember']);
    setcookie('remember', '', time() - 10, '/');
}

// Redirect ke halaman login
header("Location: index.php");
exit();
?>
