<?php
class Database {
    private $host = "localhost";
    private $db_name = "uas_lab_booking"; // Pastikan sama dengan nama DB kamu
    private $username = "root";
    private $password = "";
    public $conn;

    // Method untuk mendapatkan koneksi
    public function getConnection() {
        $this->conn = null;

        try {
            // Menggunakan Library MySQLi (Object Oriented)
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            // Cek error koneksi
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
        } catch(Exception $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>