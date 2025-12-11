<?php
class Barang {
    private $conn;
    private $table_name = "barang";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Method untuk mengambil semua data barang
    public function tampilSemua() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $result = $this->conn->query($query);
        return $result;
    }
}
?>