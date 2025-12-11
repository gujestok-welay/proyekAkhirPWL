<?php
class Barang
{
    private $conn;
    private $table_name = "barang";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // [UPGRADE] Method untuk Auto-Generate Kode Barang
    private function generateKode()
    {
        // Query untuk ambil kode_barang terakhir
        $query = "SELECT kode_barang FROM " . $this->table_name . " ORDER BY id DESC LIMIT 1";
        $result = $this->conn->query($query);

        // Jika tabel masih kosong, mulai dari BRG-001
        if ($result->num_rows == 0) {
            return "BRG-001";
        }

        // Ambil kode terakhir
        $row = $result->fetch_assoc();
        $lastKode = $row['kode_barang']; // Contoh: BRG-009

        // Pisahkan prefix dan angka (BRG-009 -> angka = 009)
        $parts = explode('-', $lastKode);
        $number = (int)$parts[1]; // Convert "009" jadi 9

        // Increment angka
        $newNumber = $number + 1;

        // Format ulang dengan padding 3 digit (10 -> 010)
        $newKode = "BRG-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return $newKode;
    }

    // Method Public untuk dipanggil dari luar class
    public function getKodeBaru()
    {
        return $this->generateKode();
    }

    public function tampilSemua()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $result = $this->conn->query($query);
        return $result;
    }

    // [MODUL 4] Fungsi Tambah Data
    public function tambahBarang($kode, $nama, $deskripsi, $stok, $gambar)
    {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table_name . " (kode_barang, nama_barang, deskripsi, stok, gambar) VALUES (?, ?, ?, ?, ?)");

        if (!$stmt) {
            // Jika prepare gagal, tampilkan error
            die("Error prepare statement: " . $this->conn->error);
        }

        $stmt->bind_param("sssis", $kode, $nama, $deskripsi, $stok, $gambar);

        if ($stmt->execute()) {
            return true;
        } else {
            // Tampilkan error eksekusi
            die("Error execute: " . $stmt->error);
        }
        return false;
    }

    // [MODUL 4] Fungsi Hapus Data
    public function hapusBarang($id)
    {
        // Ambil nama gambar dulu untuk dihapus dari folder (biar bersih)
        $query = "SELECT gambar FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // [MODUL 5] Hapus file fisik jika bukan default
        if ($row && $row['gambar'] != 'default.jpg' && file_exists("../uploads/" . $row['gambar'])) {
            unlink("../uploads/" . $row['gambar']);
        }

        // Hapus data di database
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table_name . " WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
