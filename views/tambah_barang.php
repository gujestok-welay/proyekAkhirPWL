<?php
session_start();
// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: ../index.php");
    exit;
}

include_once '../config/Database.php';
include_once '../classes/Barang.php';

$database = new Database();
$db = $database->getConnection();
$barang = new Barang($db);

// [UPGRADE] Generate Kode Barang Otomatis
$kodeBarangBaru = $barang->getKodeBaru();

// [MODUL 5] Logika Upload & Submit
if (isset($_POST['submit'])) {
    $kode = $_POST['kode_barang'];
    $nama = $_POST['nama_barang'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];

    // Proses Upload Gambar
    $gambar = "default.jpg"; // Default jika tidak ada upload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $target_dir = "../uploads/";
        // Pastikan folder uploads ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = $_FILES['gambar']['name'];
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validasi ekstensi
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_ext)) {
            // Rename file agar unik (Modul 5)
            $new_name = uniqid() . '.' . $file_ext;
            if (move_uploaded_file($file_tmp, $target_dir . $new_name)) {
                $gambar = $new_name;
            }
        } else {
            $error = "Ekstensi file tidak valid! Hanya JPG, PNG, GIF.";
        }
    }

    if (!isset($error)) {
        if ($barang->tambahBarang($kode, $nama, $deskripsi, $stok, $gambar)) {
            header("Location: dashboard.php?page=barang&msg=added");
            exit;
        } else {
            $error = "Gagal menyimpan data ke database. Error: " . $db->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>Tambah Barang Baru</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Kode Barang <span class="badge bg-success">Auto Generate</span></label>
                        <input type="text" name="kode_barang" class="form-control" value="<?= $kodeBarangBaru ?>" readonly required>
                        <small class="text-muted">Kode otomatis, tidak bisa diubah</small>
                    </div>
                    <div class="mb-3">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Stok Awal</label>
                        <input type="number" name="stok" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Foto Barang</label>
                        <input type="file" name="gambar" class="form-control">
                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                    </div>
                    <button type="submit" name="submit" class="btn btn-success">Simpan Barang</button>
                    <a href="dashboard.php?page=barang" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>