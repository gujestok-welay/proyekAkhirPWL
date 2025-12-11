<?php
class Ruangan
{
    private $conn;
    private $table_name = "ruangan";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // [UPGRADE] Method untuk Auto-Generate Kode Ruangan
    private function generateKode()
    {
        // [FIX] Cek nama kolom yang tersedia (kode_ruangan atau kode_ruang)
        $checkColumns = $this->conn->query("SHOW COLUMNS FROM " . $this->table_name);
        $kodeColumn = 'kode_ruangan'; // Default

        while ($col = $checkColumns->fetch_assoc()) {
            if ($col['Field'] == 'kode_ruang') {
                $kodeColumn = 'kode_ruang';
                break;
            }
            if ($col['Field'] == 'kode_ruangan') {
                $kodeColumn = 'kode_ruangan';
                break;
            }
        }

        // Query untuk ambil kode terakhir dengan nama kolom yang tepat
        $query = "SELECT " . $kodeColumn . " FROM " . $this->table_name . " ORDER BY id DESC LIMIT 1";
        $result = $this->conn->query($query);

        // Error handling jika query gagal
        if (!$result) {
            die("Error query generateKode: " . $this->conn->error);
        }

        // Jika tabel masih kosong, mulai dari LAB-001
        if ($result->num_rows == 0) {
            return "LAB-001";
        }

        // Ambil kode terakhir
        $row = $result->fetch_assoc();
        $lastKode = $row[$kodeColumn]; // Contoh: LAB-009

        // Pisahkan prefix dan angka (LAB-009 -> angka = 009)
        $parts = explode('-', $lastKode);
        $number = (int)$parts[1]; // Convert "009" jadi 9

        // Increment angka
        $newNumber = $number + 1;

        // Format ulang dengan padding 3 digit (10 -> 010)
        $newKode = "LAB-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

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

    // [MODUL 4] Fungsi Hitung Jumlah Total Ruangan
    public function hitungJumlah()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // [MODUL 4] Fungsi Tambah Data Ruangan
    public function tambahRuangan($kode, $nama, $lokasi, $kapasitas, $fasilitas, $gambar)
    {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table_name . " (kode_ruangan, nama_ruangan, lokasi, kapasitas, fasilitas, gambar) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Error prepare statement: " . $this->conn->error);
        }

        $stmt->bind_param("sssiss", $kode, $nama, $lokasi, $kapasitas, $fasilitas, $gambar);

        if ($stmt->execute()) {
            return true;
        } else {
            die("Error execute: " . $stmt->error);
        }
        return false;
    }

    // [MODUL 4] Fungsi Hapus Data Ruangan
    public function hapusRuangan($id)
    {
        // Ambil nama gambar dulu untuk dihapus dari folder
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

    // [MODUL 4] Fungsi untuk mengambil data single row berdasarkan ID
    public function getRuanganById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // [MODUL 4 & 5] Fungsi Update dengan penanganan gambar
    public function updateRuangan($id, $nama, $lokasi, $kapasitas, $fasilitas, $gambar)
    {
        // Ambil data lama untuk cek gambar existing
        $dataLama = $this->getRuanganById($id);

        // [MODUL 5] CRITICAL LOGIC: Hapus gambar lama jika ada gambar baru
        if (!empty($gambar) && isset($dataLama['gambar']) && $gambar != $dataLama['gambar']) {
            // Hapus file fisik gambar lama (jika bukan default.jpg)
            if ($dataLama['gambar'] != 'default.jpg' && file_exists("../uploads/" . $dataLama['gambar'])) {
                unlink("../uploads/" . $dataLama['gambar']);
            }
        }

        // Jika parameter $gambar kosong, gunakan gambar lama
        if (empty($gambar)) {
            $gambar = isset($dataLama['gambar']) ? $dataLama['gambar'] : 'default.jpg';
        }

        // [FIX] Cek kolom mana yang ada di database untuk backward compatibility
        // Query untuk cek struktur tabel
        $checkColumns = $this->conn->query("SHOW COLUMNS FROM " . $this->table_name);
        $columns = [];
        while ($col = $checkColumns->fetch_assoc()) {
            $columns[] = $col['Field'];
        }

        // Build dynamic query based on available columns
        $updateFields = ["nama_ruangan = ?"];
        $types = "s";
        $params = [$nama];

        if (in_array('lokasi', $columns)) {
            $updateFields[] = "lokasi = ?";
            $types .= "s";
            $params[] = $lokasi;
        }

        if (in_array('kapasitas', $columns)) {
            $updateFields[] = "kapasitas = ?";
            $types .= "i";
            $params[] = $kapasitas;
        }

        if (in_array('fasilitas', $columns)) {
            $updateFields[] = "fasilitas = ?";
            $types .= "s";
            $params[] = $fasilitas;
        }

        if (in_array('gambar', $columns)) {
            $updateFields[] = "gambar = ?";
            $types .= "s";
            $params[] = $gambar;
        }

        // Add ID parameter
        $types .= "i";
        $params[] = $id;

        // Build final query
        $query = "UPDATE " . $this->table_name . " SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            die("Error prepare statement: " . $this->conn->error);
        }

        // Bind parameters dynamically
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
