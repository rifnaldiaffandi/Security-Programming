<?php
// Memulai atau melanjutkan sesi PHP yang ada
session_start();

// Memeriksa apakah ada data pengguna (user) yang tersimpan dalam sesi
if (isset($_SESSION['user'])) {
    // Jika ada pengguna yang sudah login, arahkan ke halaman dashboard yang sesuai dengan peran (role) pengguna
    header("Location: dashboard_" . $_SESSION['user']['role'] . ".php");
    // Keluar dari skrip PHP agar tidak ada eksekusi lebih lanjut
    exit();
}

// Jika tidak ada pengguna yang sudah login, atur header kebijakan keamanan konten untuk membatasi sumber daya yang dapat dimuat oleh halaman
header("Content-Security-Policy: default-src 'self';");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/login.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <label>NIM</label>
            <input type="text" name="nim" required><br>
            <label>Password</label>
            <input type="password" name="password" required><br>
            <div class="remember">
                <input type="checkbox" class ="remember" name="remember"> Remember Me
            </div>
            <button type="submit">Login</button>
            <p>Belum punya akun? <a href="register.php">Register</a></p>
        </form>
    </div>
    
</body>
</html>
