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

// [MODUL 3] Ambil ID dari parameter GET
if (!isset($_GET['id'])) {
    header("Location: dashboard.php?page=ruangan");
    exit;
}

$id = $_GET['id'];
$dataRuangan = $ruangan->getRuanganById($id);

// Jika data tidak ditemukan
if (!$dataRuangan) {
    header("Location: dashboard.php?page=ruangan&msg=notfound");
    exit;
}

// [MODUL 5] Logika Upload & Submit Edit
if (isset($_POST['submit'])) {
    $nama = $_POST['nama_ruangan'];
    $lokasi = $_POST['lokasi'];
    $kapasitas = $_POST['kapasitas'];
    $fasilitas = $_POST['fasilitas'];
    $gambarLama = $_POST['gambar_lama']; // Hidden input

    // Proses Upload Gambar (Jika user upload file baru)
    $gambarBaru = ""; // Default kosong
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = $_FILES['gambar']['name'];
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validasi ekstensi
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_ext)) {
            // Validasi ukuran file (max 2MB)
            if ($_FILES['gambar']['size'] <= 2097152) {
                // Rename file agar unik (Modul 5)
                $new_name = uniqid() . '.' . $file_ext;
                if (move_uploaded_file($file_tmp, $target_dir . $new_name)) {
                    $gambarBaru = $new_name;
                }
            } else {
                $error = "Ukuran file terlalu besar! Max 2MB.";
            }
        } else {
            $error = "Ekstensi file tidak valid! Hanya JPG, PNG, GIF.";
        }
    }

    if (!isset($error)) {
        // Jika ada gambar baru, gunakan gambar baru. Jika tidak, tetap pakai gambar lama
        $gambarFinal = !empty($gambarBaru) ? $gambarBaru : $gambarLama;

        if ($ruangan->updateRuangan($id, $nama, $lokasi, $kapasitas, $fasilitas, $gambarFinal)) {
            header("Location: dashboard.php?page=ruangan&msg=updated");
            exit;
        } else {
            $error = "Gagal mengupdate data ke database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Ruangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4><i class="fas fa-edit"></i> Edit Data Ruangan</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <!-- Hidden input untuk menyimpan nama gambar lama -->
                    <input type="hidden" name="gambar_lama" value="<?= $dataRuangan['gambar'] ?? 'default.jpg' ?>">

                    <div class="mb-3">
                        <label>Kode Ruangan <span class="badge bg-secondary">Tidak dapat diubah</span></label>
                        <input type="text" name="kode_ruangan" class="form-control" value="<?= $dataRuangan['kode_ruangan'] ?? $dataRuangan['kode_ruang'] ?? 'N/A' ?>" readonly>
                        <small class="text-muted">Primary key tidak bisa diubah</small>
                    </div>
                    <div class="mb-3">
                        <label>Nama Ruangan</label>
                        <input type="text" name="nama_ruangan" class="form-control" value="<?= htmlspecialchars($dataRuangan['nama_ruangan'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($dataRuangan['lokasi'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Kapasitas (Orang)</label>
                        <input type="number" name="kapasitas" class="form-control" value="<?= $dataRuangan['kapasitas'] ?? 0 ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Fasilitas</label>
                        <textarea name="fasilitas" class="form-control" rows="3"><?= htmlspecialchars($dataRuangan['fasilitas'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Foto Ruangan</label>

                        <!-- [MODUL 5] Tampilkan gambar current sebagai thumbnail -->
                        <div class="mb-2">
                            <?php
                            $currentImage = $dataRuangan['gambar'] ?? 'default.jpg';
                            $imagePath = "../uploads/" . $currentImage;
                            if (!file_exists($imagePath)) {
                                $currentImage = 'default.jpg';
                            }
                            ?>
                            <img src="../uploads/<?= $currentImage ?>" alt="Current Image" width="150" height="150" style="object-fit: cover; border: 2px solid #ddd; border-radius: 8px;">
                            <br>
                            <small class="text-muted">Gambar saat ini: <strong><?= $currentImage ?></strong></small>
                        </div>

                        <input type="file" name="gambar" class="form-control">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar. Format: JPG, PNG, GIF. Max: 2MB</small>
                    </div>

                    <button type="submit" name="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Ruangan
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