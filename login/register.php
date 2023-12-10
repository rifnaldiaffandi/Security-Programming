<?php
// Memulai sesi PHP (jika belum dimulai sebelumnya)
session_start();

// Menginclude file functions.php yang berisi fungsi-fungsi yang akan digunakan
include 'config/functions.php';

// Memeriksa apakah permintaan (request) ke server merupakan metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil nilai input dari formulir yang disubmit menggunakan metode POST
    $username = $_POST['username'];
    $nim = $_POST['nim'];
    $password = $_POST['password'];
    
    // Memeriksa apakah NIM sudah ada dalam database
    if (isNIMExist($nim)) {
        // Jika NIM sudah ada, mencetak pesan kesalahan
        echo "NIM sudah digunakan. Silakan gunakan NIM lain.";
    } else {
        // Jika NIM belum ada, melakukan registrasi
        if (register($username, $nim, $password)) {
            // Jika registrasi berhasil, redirect ke halaman index.php
            header("Location: index.php");
            exit(); // Menghentikan eksekusi skrip PHP setelah melakukan redirect
        } else {
            // Jika registrasi gagal, mencetak pesan kesalahan
            echo "Registrasi Gagal";
        }
    }
}

// Mengatur header untuk Content-Security-Policy, membatasi sumber daya yang dapat dimuat oleh halaman
header("Content-Security-Policy: default-src 'self';");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/register.css">
    <title>Register</title>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="register.php" method="post">
            <label>Username:</label>
            <input type="text" name="username" required><br>
            <label>NIM:</label>
            <input type="text" name="nim" required><br>
            <label>Password:</label>
            <input type="password" name="password" required><br>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
