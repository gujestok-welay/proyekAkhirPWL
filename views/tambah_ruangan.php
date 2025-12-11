<?php
session_start();
// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: ../index.php");
    exit;
}

include_once '../config/Database.php';
include_once '../classes/Ruangan.php';

$database = new Database();
$db = $database->getConnection();
$ruangan = new Ruangan($db);

// [UPGRADE] Generate Kode Ruangan Otomatis
$kodeRuanganBaru = $ruangan->getKodeBaru();

// [MODUL 5] Logika Upload & Submit
if (isset($_POST['submit'])) {
    $kode = $_POST['kode_ruangan'];
    $nama = $_POST['nama_ruangan'];
    $lokasi = $_POST['lokasi'];
    $kapasitas = $_POST['kapasitas'];
    $fasilitas = $_POST['fasilitas'];

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
        if ($ruangan->tambahRuangan($kode, $nama, $lokasi, $kapasitas, $fasilitas, $gambar)) {
            header("Location: dashboard.php?page=ruangan&msg=added");
            exit;
        } else {
            $error = "Gagal menyimpan data ke database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Ruangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4><i class="fas fa-door-open"></i> Tambah Ruangan Baru</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Kode Ruangan <span class="badge bg-success">Auto Generate</span></label>
                        <input type="text" name="kode_ruangan" class="form-control" value="<?= $kodeRuanganBaru ?>" readonly required>
                        <small class="text-muted">Kode otomatis, tidak bisa diubah</small>
                    </div>
                    <div class="mb-3">
                        <label>Nama Ruangan</label>
                        <input type="text" name="nama_ruangan" class="form-control" placeholder="Contoh: Lab Komputer 1" required>
                    </div>
                    <div class="mb-3">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Gedung A Lt. 2" required>
                    </div>
                    <div class="mb-3">
                        <label>Kapasitas (Orang)</label>
                        <input type="number" name="kapasitas" class="form-control" placeholder="Contoh: 40" required>
                    </div>
                    <div class="mb-3">
                        <label>Fasilitas</label>
                        <textarea name="fasilitas" class="form-control" rows="3" placeholder="Contoh: AC, Proyektor, 40 PC"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Foto Ruangan</label>
                        <input type="file" name="gambar" class="form-control">
                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                    </div>
                    <button type="submit" name="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Ruangan
                    </button>
                    <a href="dashboard.php?page=ruangan" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>