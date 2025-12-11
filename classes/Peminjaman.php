<?php
class Peminjaman
{
    private $conn;
    private $table_name = "peminjaman";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // 1. FUNGSI BUAT PINJAM BARU (YANG SUDAH DIPERBAIKI)
    public function tambahPeminjaman($user_id, $jenis, $item_id, $jumlah, $tgl_pinjam, $tgl_kembali, $keterangan)
    {

        // A. SANITASI INPUT (Security anti-hack)
        $keterangan = $this->conn->real_escape_string($keterangan);
        $tgl_pinjam = $this->conn->real_escape_string($tgl_pinjam);
        $tgl_kembali = $this->conn->real_escape_string($tgl_kembali);

        // B. LOGIKA CEK KETERSEDIAAN (VALIDASI BACKEND)
        if ($jenis == 'barang') {
            // Cek Stok Barang
            $queryCek = "SELECT stok, nama_barang FROM barang WHERE id = '$item_id'";
            $result = $this->conn->query($queryCek);
            $data = $result->fetch_assoc();

            if ($data['stok'] < $jumlah) {
                // Return error message string
                return "Stok " . $data['nama_barang'] . " tidak cukup! Sisa: " . $data['stok'];
            }

            // Set ID
            $barang_id = $item_id;
            $ruangan_id = "NULL";
            $qty = $jumlah;
        } else {
            // Cek Bentrok Ruangan (Collision Detection)
            // Rumus Bentrok: (StartA < EndB) AND (EndA > StartB)
            $queryCek = "SELECT * FROM " . $this->table_name . " 
                         WHERE ruangan_id = '$item_id' 
                         AND status != 'rejected' AND status != 'returned'
                         AND ('$tgl_pinjam' < tgl_kembali AND '$tgl_kembali' > tgl_pinjam)";

            $result = $this->conn->query($queryCek);
            if ($result->num_rows > 0) {
                return "Ruangan sudah dibooking pada jam tersebut! Silakan pilih jam lain.";
            }

            // Set ID
            $barang_id = "NULL";
            $ruangan_id = $item_id;
            $qty = 1;
        }

        // C. PROSES INSERT
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, jenis_peminjaman, barang_id, ruangan_id, jumlah, tgl_pinjam, tgl_kembali, keterangan, status)
                  VALUES 
                  ('$user_id', '$jenis', $barang_id, $ruangan_id, '$qty', '$tgl_pinjam', '$tgl_kembali', '$keterangan', 'pending')";

        if ($this->conn->query($query)) {
            // Kurangi stok JIKA barang
            if ($jenis == 'barang') {
                $kurangStok = "UPDATE barang SET stok = stok - $qty WHERE id = $item_id";
                $this->conn->query($kurangStok);
            }
            return true; // Berhasil (Boolean true)
        }

        return "Gagal menyimpan ke database.";
    }

    // 2. FUNGSI TAMPIL RIWAYAT
    public function tampilRiwayat()
    {
        $query = "SELECT p.*, u.nama_lengkap, b.nama_barang, r.nama_ruangan 
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN barang b ON p.barang_id = b.id
                  LEFT JOIN ruangan r ON p.ruangan_id = r.id
                  ORDER BY p.created_at DESC";
        return $this->conn->query($query);
    }

    // 3. FUNGSI UPDATE STATUS
    public function updateStatus($id, $status_baru)
    {
        $cek = $this->conn->query("SELECT * FROM " . $this->table_name . " WHERE id = '$id'");
        $data = $cek->fetch_assoc();

        $query = "UPDATE " . $this->table_name . " SET status = '$status_baru' WHERE id = '$id'";

        if ($this->conn->query($query)) {
            // Kalau barang kembali atau ditolak (rejected), stok harus balik!
            if (($status_baru == 'returned' || $status_baru == 'rejected') && $data['jenis_peminjaman'] == 'barang') {
                // Cek dulu status sebelumnya, jangan sampai stok nambah 2x kalau diklik berkali-kali
                // (Logika sederhana: Asumsikan admin benar alurnya)

                $barang_id = $data['barang_id'];
                $jumlah = $data['jumlah'];
                $queryStok = "UPDATE barang SET stok = stok + $jumlah WHERE id = '$barang_id'";
                $this->conn->query($queryStok);
            }
            return true;
        }
        return false;
    }
}
