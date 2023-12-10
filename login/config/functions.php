<?php
include 'db.php';

//PDO digunakan untuk berinteraksi dengan database MySQL. 

// Definisikan fungsi login dengan parameter $nim, $password, dan $remember_me_token (default: false)
function login($nim, $password, $remember_me_token = false) {
    // Panggil fungsi getUserByNIM untuk mendapatkan informasi pengguna berdasarkan NIM
    $user = getUserByNIM($nim);

    // Ambil variabel global $conn yang merupakan koneksi ke database
    global $conn;

    // Persiapkan query SQL untuk mengambil informasi pengguna berdasarkan NIM
    $stmt = $conn->prepare("SELECT * FROM users WHERE nim = :nim");
    $stmt->bindParam(':nim', $nim);
    $stmt->execute();

    // Ambil hasil query dan simpan dalam variabel $user sebagai array asosiatif
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifikasi password menggunakan password_verify
    if ($user && password_verify($password, $user['password'])) {
        // Reset counter login attempts jika login berhasil
        updateLoginAttempts($user['id'], 0);

        // Set informasi pengguna ke dalam session
        $_SESSION['user'] = $user;

        // Set cookie "Remember Me" jika dicentang
        if ($remember_me_token) {
            setRememberMeCookie($user['id']);
        }

        // Mengembalikan true sebagai tanda bahwa login berhasil
        return true;
    } else {
        // Jika login gagal, tambahkan counter login attempts
        $attempts = getLoginAttempts($nim);
        updateLoginAttempts($user['id'], $attempts + 1);

        // Mengembalikan false sebagai tanda bahwa login gagal
        return false;
    }
}

// Function untuk mengatur cookie "Remember Me" berdasarkan user ID
function setRememberMeCookie($userId) {
    // Menggunakan variabel global $conn yang diasumsikan telah didefinisikan di tempat lain
    global $conn;

    // Buat token unik untuk "Remember Me" dengan menggunakan fungsi random_bytes() dan bin2hex() untuk mengonversi menjadi string hexadecimal
    $token = bin2hex(random_bytes(32));
    
    // Waktu kadaluwarsa token diatur menjadi 30 hari dari waktu saat ini
    $expireTime = time() + (30 * 24 * 60 * 60);

    // Persiapkan statement SQL untuk mengupdate tabel users dengan token dan waktu kadaluwarsa yang baru
    $stmt = $conn->prepare("UPDATE users SET remember_me_token = :token, remember_me_expire = :expire WHERE id = :id");
    
    // Bind parameter pada statement SQL dengan nilai yang sesuai
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':expire', date('Y-m-d H:i:s', $expireTime));
    $stmt->bindParam(':id', $userId);
    
    // Eksekusi statement SQL untuk mengupdate data di database
    $stmt->execute();

    // Set cookie yang berisi token "Remember Me" dengan menggunakan setcookie()
    $cookieValue = base64_encode("{$userId}:{$token}");
    setcookie('remember', $cookieValue, $expireTime, '/', '', true, true);
}



// Fungsi untuk mendapatkan jumlah percobaan login berdasarkan NIM
function getLoginAttempts($nim) {
    global $conn;
    // Menyiapkan query SQL untuk mengambil jumlah login_attempts dari tabel users berdasarkan NIM
    $stmt = $conn->prepare("SELECT login_attempts FROM users WHERE nim = :nim");
    // Mengikat parameter NIM ke placeholder dalam query SQL
    $stmt->bindParam(':nim', $nim);
    // Mengeksekusi query SQL
    $stmt->execute();
    // Mengembalikan hasil fetchColumn() yang berisi jumlah login_attempts
    return $stmt->fetchColumn();
}

// Fungsi untuk memperbarui jumlah percobaan login pada suatu pengguna berdasarkan ID pengguna
function updateLoginAttempts($userId, $attempts) {
    global $conn;
    // Menyiapkan query SQL untuk memperbarui login_attempts dalam tabel users berdasarkan ID pengguna
    $stmt = $conn->prepare("UPDATE users SET login_attempts = :attempts WHERE id = :id");
    // Mengikat parameter attempts dan ID ke placeholder dalam query SQL
    $stmt->bindParam(':attempts', $attempts);
    $stmt->bindParam(':id', $userId);
    // Mengeksekusi query SQL
    $stmt->execute();
}

// Fungsi untuk mendaftarkan pengguna baru ke dalam sistem
function register($username, $nim, $password) {
    global $conn;
    // Melakukan hash terhadap password menggunakan algoritma yang aman
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Setiap registrasi dianggap sebagai peran "user"
    $role = 'user';

    // Menyiapkan query SQL untuk memasukkan data pengguna baru ke dalam tabel users
    $stmt = $conn->prepare("INSERT INTO users (username, nim, password, role) VALUES (:username, :nim, :password, :role)");
    // Mengikat parameter-parameter ke placeholder dalam query SQL
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':nim', $nim);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':role', $role);

    // Mengeksekusi query SQL dan mengembalikan hasil eksekusi
    return $stmt->execute();
}

// Fungsi untuk menambahkan data nilai ke dalam tabel nilai
function addDataNilai($nim, $nama, $asg, $uts, $uas) {
    global $conn;
    // Menyiapkan query SQL untuk memasukkan data nilai ke dalam tabel nilai
    $stmt = $conn->prepare("INSERT INTO nilai (nim, nama, asg, uts, uas) VALUES (:nim, :nama, :asg, :uts, :uas)");
    // Mengikat parameter-parameter ke placeholder dalam query SQL
    $stmt->bindParam(':nim', $nim);
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':asg', $asg);
    $stmt->bindParam(':uts', $uts);
    $stmt->bindParam(':uas', $uas);
    // Mengeksekusi query SQL dan mengembalikan hasil eksekusi
    return $stmt->execute();
}


// Fungsi untuk mengupdate data nilai berdasarkan ID
function updateDataNilai($id, $nama, $asg, $uts, $uas) {
    global $conn;
    
    // Persiapkan query SQL untuk melakukan UPDATE pada tabel nilai
    $stmt = $conn->prepare("UPDATE nilai SET nama = :nama, asg = :asg, uts = :uts, uas = :uas WHERE id = :id");
    
    // Binding parameter untuk menghindari SQL injection dan memasukkan nilai ke dalam query
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':asg', $asg);
    $stmt->bindParam(':uts', $uts);
    $stmt->bindParam(':uas', $uas);
    
    // Eksekusi query UPDATE dan kembalikan hasilnya
    return $stmt->execute();
}

// Fungsi untuk menghapus data nilai berdasarkan ID
function deleteDataNilai($id) {
    global $conn;
    
    // Persiapkan query SQL untuk melakukan DELETE pada tabel nilai
    $stmt = $conn->prepare("DELETE FROM nilai WHERE id = :id");
    
    // Binding parameter untuk menghindari SQL injection dan memasukkan nilai ke dalam query
    $stmt->bindParam(':id', $id);
    
    // Eksekusi query DELETE dan kembalikan hasilnya
    return $stmt->execute();
}

// Fungsi untuk mendapatkan semua data nilai dari tabel
function getDataNilai() {
    global $conn;
    
    // Eksekusi query SELECT untuk mendapatkan semua data dari tabel nilai
    $stmt = $conn->query("SELECT * FROM nilai");
    
    // Kembalikan hasil fetch dalam bentuk array asosiatif
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi untuk mendapatkan data nilai berdasarkan NIM
function getDataNilaiByNIM($nim) {
    global $conn;
    
    // Persiapkan query SQL untuk melakukan SELECT dengan kondisi NIM pada tabel nilai
    $stmt = $conn->prepare("SELECT * FROM nilai WHERE nim = :nim");
    
    // Binding parameter untuk menghindari SQL injection dan memasukkan nilai ke dalam query
    $stmt->bindParam(':nim', $nim);
    
    // Eksekusi query SELECT dan kembalikan hasil fetch dalam bentuk array asosiatif
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi untuk mendapatkan data nilai berdasarkan ID
function getDataNilaiById($id) {
    global $conn;
    
    // Persiapkan query SQL untuk melakukan SELECT dengan kondisi ID pada tabel nilai
    $stmt = $conn->prepare("SELECT * FROM nilai WHERE id = :id");
    
    // Binding parameter untuk menghindari SQL injection dan memasukkan nilai ke dalam query
    $stmt->bindParam(':id', $id);
    
    // Eksekusi query SELECT dan kembalikan hasil fetch dalam bentuk array asosiatif
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fungsi untuk membuat user baru
function createUser($username, $nim, $password) {
    global $conn;
    
    // Persiapkan query SQL untuk melakukan INSERT pada tabel users
    $stmt = $conn->prepare("INSERT INTO users (username, nim, password) VALUES (:username, :nim, :password)");
    
    // Binding parameter untuk menghindari SQL injection dan memasukkan nilai ke dalam query
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':nim', $nim);
    $stmt->bindParam(':password', $password);
    
    // Eksekusi query INSERT dan kembalikan hasilnya
    return $stmt->execute();
}

// Fungsi untuk mendapatkan data user berdasarkan NIM
function getUserByNIM($nim) {
    global $conn;
    
    // Persiapkan query SQL untuk melakukan SELECT dengan kondisi NIM pada tabel users
    $stmt = $conn->prepare("SELECT * FROM users WHERE nim = :nim");
    
    // Binding parameter untuk menghindari SQL injection dan memasukkan nilai ke dalam query
    $stmt->bindParam(':nim', $nim);
    
    // Eksekusi query SELECT dan kembalikan hasil fetch dalam bentuk array asosiatif
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// Fungsi untuk mendapatkan daftar semua dosen
function getDaftarDosen() {
    global $conn; // Menggunakan koneksi database global
    $stmt = $conn->query("SELECT * FROM dosen"); // Mengirim query SQL untuk mengambil semua data dari tabel dosen
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Mengembalikan hasil query dalam bentuk array asosiatif
}

// Fungsi untuk memeriksa apakah NIM sudah ada dalam tabel users
function isNIMExist($nim) {
    global $conn; // Menggunakan koneksi database global
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE nim = :nim"); // Menggunakan prepared statement untuk mencegah SQL injection
    $stmt->bindParam(':nim', $nim); // Mengikat parameter nim ke placeholder :nim
    $stmt->execute(); // Menjalankan query
    return $stmt->fetchColumn() > 0; // Mengembalikan true jika jumlah baris yang ditemukan lebih dari 0, false jika sebaliknya
}

// Fungsi untuk memeriksa apakah akun terkunci berdasarkan jumlah percobaan login
function isLockedOut($nim) {
    global $conn; // Menggunakan koneksi database global
    $stmt = $conn->prepare("SELECT login_attempts FROM users WHERE nim = :nim"); // Menggunakan prepared statement untuk mencegah SQL injection
    $stmt->bindParam(':nim', $nim); // Mengikat parameter nim ke placeholder :nim
    $stmt->execute(); // Menjalankan query
    $attempts = $stmt->fetchColumn(); // Mengambil nilai jumlah percobaan login dari hasil query
    return $attempts >= MAX_LOGIN_ATTEMPTS; // Mengembalikan true jika jumlah percobaan login melebihi batas maksimum, false jika sebaliknya
}

// Fungsi untuk mengunci akun dengan mengatur jumlah percobaan login dan waktu terkunci
function lockoutUser($nim) {
    global $conn; // Menggunakan koneksi database global
    $stmt = $conn->prepare("UPDATE users SET login_attempts = :attempts, locked_out_until = :lockout WHERE nim = :nim"); // Menggunakan prepared statement untuk mencegah SQL injection
    $stmt->bindParam(':attempts', 0); // Mengatur jumlah percobaan login menjadi 0 setelah mengunci
    $stmt->bindParam(':lockout', time() + LOCKOUT_DURATION); // Mengatur waktu terkunci berdasarkan durasi terkunci yang ditentukan
    $stmt->bindParam(':nim', $nim); // Mengikat parameter nim ke placeholder :nim
    $stmt->execute(); // Menjalankan query untuk mengupdate data pengguna
}



?>

