<?php
class Auth {
    private $conn;

    // Konstruktor: Saat class dipanggil, dia butuh koneksi database
    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        // Mencegah SQL Injection sederhana
        $username = $this->conn->real_escape_string($username);
        
        // Enkripsi password dengan MD5 (Sesuai data dummy SQL tadi)
        // Note: Untuk project real, disarankan pakai password_hash/bcrypt
        $password = md5($password); 

        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Simpan data user ke SESSION biar sistem tahu siapa yang login
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nama'] = $row['nama_lengkap'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['status'] = "login";
            
            return true; // Login Berhasil
        } else {
            return false; // Login Gagal
        }
    }

    public function logout() {
        session_destroy();
        return true;
    }
    
    // Fungsi untuk memproteksi halaman (Cek apakah user sudah login?)
    public function checkLogin() {
        if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
            header("location: index.php"); // Tendang balik ke login
            exit;
        }
    }
}
?>