<?php
class Ruangan {
    private $conn;
    private $table_name = "ruangan";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function tampilSemua() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $result = $this->conn->query($query);
        return $result;
    }
}
?>