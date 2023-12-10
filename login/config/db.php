<?php
// Inisialisasi variabel-variabel yang digunakan untuk koneksi ke database
$host = 'localhost'; // Nama host database (dalam hal ini, localhost)
$dbname = 'multiuser'; // Nama database yang ingin diakses
$username = 'root'; // Nama pengguna database
$password = ''; // Kata sandi pengguna database

// Blok try-catch untuk menangani koneksi ke database menggunakan PDO (PHP Data Objects)
try {
    // Membuat objek koneksi PDO dengan parameter host, database name, username, dan password
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Mengatur atribut koneksi untuk mode error reporting dan penanganan exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Menangkap dan menampilkan pesan kesalahan jika koneksi gagal
    echo "Connection failed: " . $e->getMessage();
}

// Fungsi untuk menutup koneksi database
function closeConnection() {
    global $conn; // Menggunakan variabel $conn yang dideklarasikan di luar fungsi
    $conn = null; // Menutup koneksi dengan mengatur variabel $conn menjadi null
}
?>
