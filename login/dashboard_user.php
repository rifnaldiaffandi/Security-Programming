<?php
session_start(); // Memulai atau melanjutkan sesi pengguna
include 'config/functions.php'; // Mengimpor fungsi-fungsi dari file functions.php

// Memeriksa apakah pengguna sudah login dan memiliki peran 'user'
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: index.php"); // Mengarahkan pengguna ke halaman index.php jika belum login atau bukan user
    exit();
}

$nim = $_SESSION['user']['nim']; // Mendapatkan NIM pengguna yang sedang login

header("Content-Security-Policy: default-src 'self';"); // Mengatur kebijakan keamanan konten

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/user.css"> <!-- Menyisipkan stylesheet CSS -->
    <title>Dashboard User</title>
</head>
<body>
    <div class="container">
    <h2>Welcome, User!</h2>

    <h3>Data Nilai Mahasiswa</h3>
    <table border="1">
        <tr>
            <th>NIM</th>
            <th>Nama</th>
            <th>Assignment</th>
            <th>UTS</th>
            <th>UAS</th>
        </tr>
        <?php
        $dataNilai = getDataNilaiByNIM($nim); // Memanggil fungsi untuk mendapatkan nilai berdasarkan NIM
        foreach ($dataNilai as $nilai) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($nilai['nim']) . "</td>"; // Menampilkan NIM dengan menghindari injeksi HTML
            echo "<td>" . htmlspecialchars($nilai['nama']) . "</td>"; // Menampilkan nama dengan menghindari injeksi HTML
            echo "<td>" . htmlspecialchars($nilai['asg']) . "</td>"; // Menampilkan nilai assignment dengan menghindari injeksi HTML
            echo "<td>" . htmlspecialchars($nilai['uts']) . "</td>"; // Menampilkan nilai UTS dengan menghindari injeksi HTML
            echo "<td>" . htmlspecialchars($nilai['uas']) . "</td>"; // Menampilkan nilai UAS dengan menghindari injeksi HTML
            echo "</tr>";
        }
        ?>
    </table>

    <!-- <h3>Daftar Dosen</h3>
    <ul>
        php
        $daftarDosen = getDaftarDosen();
        foreach ($daftarDosen as $dosen) {
            echo "<li>{$dosen['nama']}</li>";
        }
        ?>
    </ul> -->

    <button href="logout.php" class="logout-button">
        <a href="logout.php" class="logout-button">Log out</a> <!-- Tombol untuk logout -->
    </button>

    </div>
</body>
</html>
