<?php
// Mulai sesi PHP
session_start();

// Sertakan fungsi-fungsi yang diperlukan dari file functions.php
include 'config/functions.php';

// Cek apakah pengguna sudah login dan memiliki peran sebagai admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Cek apakah ada permintaan POST dari formulir
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Cek apakah tombol 'add' ditekan untuk menambahkan data nilai
    if (isset($_POST['add'])) {
        // Ambil data dari formulir
        $nim = $_POST['nim'];
        $nama = $_POST['nama'];
        $asg = $_POST['asg'];
        $uts = $_POST['uts'];
        $uas = $_POST['uas'];
        
        // Panggil fungsi addDataNilai untuk menambahkan data nilai
        if (addDataNilai($nim, $nama, $asg, $uts, $uas)) {
            echo "Data berhasil ditambahkan.";
        } else {
            echo "Gagal menambahkan data.";
        }
    } 
    // Cek apakah tombol 'update' ditekan untuk menampilkan form update
    elseif (isset($_POST['update'])) {
        // Ambil ID data yang akan diupdate
        $idToUpdate = $_POST['id'];
        
        // Ambil data nilai berdasarkan ID
        $dataToUpdate = getDataNilaiById($idToUpdate);
        
        // Tampilkan formulir update dengan data yang sudah ada
        echo "
        <h3>Update Data Nilai Mahasiswa</h3>
        <form action='dashboard_admin.php' method='post'>
        <input type='hidden' name='id' value='{$dataToUpdate['id']}'>
                <label>NIM:</label>
                <input type='text' name='nim' value='{$dataToUpdate['nim']}' readonly><br>
                <label>Nama:</label>
                <input type='text' name='nama' value='{$dataToUpdate['nama']}' required><br>
                <label>Assignment:</label>
                <input type='text' name='asg' value='{$dataToUpdate['asg']}' required><br>
                <label>UTS:</label>
                <input type='text' name='uts' value='{$dataToUpdate['uts']}' required><br>
                <label>UAS:</label>
                <input type='text' name='uas' value='{$dataToUpdate['uas']}' required><br>
                <button type='submit' name='confirmUpdate'>Konfirmasi Update</button>
            </form>
        ";
    } 
    // Cek apakah tombol 'confirmUpdate' ditekan untuk melakukan konfirmasi update
    elseif (isset($_POST['confirmUpdate'])) {
        // Ambil data dari formulir update
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $asg = $_POST['asg'];
        $uts = $_POST['uts'];
        $uas = $_POST['uas'];
        
        // Panggil fungsi updateDataNilai untuk memperbarui data nilai
        if (updateDataNilai($id, $nama, $asg, $uts, $uas)) {
            echo "Data berhasil diperbarui.";
        } else {
            echo "Gagal memperbarui data.";
        }
    } 
    // Cek apakah tombol 'delete' ditekan untuk menghapus data
    elseif (isset($_POST['delete'])) {
        // Ambil ID data yang akan dihapus
        $id = $_POST['id'];
        
        // Panggil fungsi deleteDataNilai untuk menghapus data nilai
        if (deleteDataNilai($id)) {
            echo "Data berhasil dihapus.";
        } else {
            echo "Gagal menghapus data.";
        }
    }
}

// Set header Content-Security-Policy
header("Content-Security-Policy: default-src 'self';");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/admin.css">
    <title>Dashboard Admin</title>
</head>
<body>
    <div class="container">
        <h2>Welcome, Admin!</h2>
        
        <!-- Formulir untuk menambah data nilai -->
        <h3>Tambah Data Nilai</h3>
        <form action="dashboard_admin.php" method="post">
            <label>NIM:</label>
            <input type="text" name="nim" required><br>
            <label>Nama:</label>
            <input type="text" name="nama" required><br>
            <label>Assignment:</label>
            <input type="text" name="asg" required><br>
            <label>UTS:</label>
            <input type="text" name="uts" required><br>
            <label>UAS:</label>
            <input type="text" name="uas" required><br>
            <button type="submit" name="add">Tambah Data</button>
        </form>

        <!-- Tabel untuk menampilkan data nilai -->
        <h3>Data Nilai Mahasiswa</h3>
        <table border="1">
            <tr>
                <th>NIM</th>
                <th>Nama</th>
                <th>Assignment</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Action</th>
            </tr>
            <?php
            // Ambil data nilai dan tampilkan dalam tabel
            $dataNilai = getDataNilai();
            foreach ($dataNilai as $nilai) {
                echo "<tr>";
                echo "<td>{$nilai['nim']}</td>";
                echo "<td>{$nilai['nama']}</td>";
                echo "<td>{$nilai['asg']}</td>";
                echo "<td>{$nilai['uts']}</td>";
                echo "<td>{$nilai['uas']}</td>";
                echo "<td>
                        <form action='dashboard_admin.php' method='post'>
                            <input type='hidden' name='id' value='{$nilai['id']}'>
                            <button type='submit' name='update'>Update</button>
                            <button type='submit' name='delete'>Delete</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
    
    <!-- Tombol Logout -->
    <a class="logout-button" href="logout.php">Logout</a>
</body>
</html>
