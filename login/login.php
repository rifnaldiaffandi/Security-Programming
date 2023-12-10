<?php
// Mulai sesi PHP (untuk penggunaan session)
session_start();

// Masukkan file functions.php yang berisi fungsi-fungsi terkait
include 'config/functions.php';

// Tentukan konstanta untuk batas maksimum percobaan login dan durasi waktu blokir
define('MAX_LOGIN_ATTEMPTS', 3);
define('LOCKOUT_DURATION', 300); // Dalam detik (300 detik = 5 menit)

// Cek apakah request yang diterima adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil nilai nim dan password dari form POST
    $nim = $_POST['nim'];
    $password = $_POST['password'];
    
    // Periksa apakah opsi "remember me" diaktifkan
    $remember_me_token = isset($_POST['remember']);

    // Periksa apakah akun sedang terkunci
    if (!isLockedOut($nim)) {
        // Jika tidak terkunci, coba melakukan login
        if (login($nim, $password, $remember_me_token)) {
            // Jika login berhasil:
            
            // Reset counter login attempts jika login berhasil
            updateLoginAttempts($_SESSION['user']['id'], 0);

            // Regenerate session ID untuk menghindari session hijacking
            session_regenerate_id(true);

            // Set cookie dengan user_id yang berlaku selama 1 jam
            setcookie('user_id', $_SESSION['user']['id'], time() + 3600, '/');

            // Redirect ke halaman dashboard sesuai peran pengguna
            header("Location: dashboard_" . $_SESSION['user']['role'] . ".php");
            exit();
        } else {
            // Jika login gagal:
            
            // Tambahkan counter login attempts
            $attempts = isset($_SESSION['user']) ? getLoginAttempts($nim) : 0;
            updateLoginAttempts($attempts + 1, $nim);
            
            // Delay untuk menghambat serangan brute force (0.5 detik)
            usleep(500000);

            // Jika mencapai batas percobaan, beri waktu blokir
            if ($attempts + 1 >= MAX_LOGIN_ATTEMPTS) {
                lockoutUser($nim);
                echo "Terlalu banyak percobaan login. Akun Anda telah terkunci. Silakan coba lagi nanti.";
            } else {
                // Menampilkan pesan jika login gagal
                echo "Login Gagal. Silakan coba lagi.";
            }
        }
    } else {
        // Jika akun terkunci
        echo "Akun Anda terkunci. Silakan coba lagi nanti.";
    }
}

// Set header untuk Content-Security-Policy
header("Content-Security-Policy: default-src 'self';");
?>
