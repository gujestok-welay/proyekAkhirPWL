<?php
session_start();
// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: ../index.php");
    exit;
}

include_once '../config/Database.php';
include_once '../classes/Barang.php';
// Sesuaikan nama file Ruang.php atau Ruangan.php sesuai file kamu
include_once '../classes/Ruangan.php';
include_once '../classes/Peminjaman.php';

$database = new Database();
$db = $database->getConnection();

$barangObj = new Barang($db);
$listBarang = $barangObj->tampilSemua();

$ruangObj = new Ruangan($db);
$listRuang = $ruangObj->tampilSemua();


// PROSES FORM JIKA DISUBMIT
if (isset($_POST['submit'])) {
    // 1. Tangkap Inputan
    $user_id = $_SESSION['user_id'];
    $jenis = $_POST['jenis_peminjaman'];
    $item_id = ($jenis == 'barang') ? $_POST['barang_id'] : $_POST['ruangan_id'];
    $jumlah = $_POST['jumlah'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'];
    $keterangan = $_POST['keterangan'];

    // 2. VALIDASI TANGGAL
    $start = strtotime($tgl_pinjam);
    $end = strtotime($tgl_kembali);
    $now = time();

    if ($end <= $start) {
        $error = "❌ Logika Salah: Tanggal Selesai harus lebih besar dari Tanggal Mulai!";
    } elseif ($start < ($now - 3600)) {
        $error = "❌ Tidak boleh meminjam di masa lalu!";
    } else {
        // 3. EKSEKUSI KE DATABASE
        $pinjam = new Peminjaman($db);
        $hasil = $pinjam->tambahPeminjaman($user_id, $jenis, $item_id, $jumlah, $tgl_pinjam, $tgl_kembali, $keterangan);

        if ($hasil === true) {
            // BERHASIL: Redirect dengan msg=sukses (biar sama dengan dashboard)
            header("Location: dashboard.php?page=peminjaman&msg=sukses");
            exit;
        } else {
            // GAGAL: Tampilkan pesan error dari Class Peminjaman (Stok habis / Bentrok)
            $error = "❌ " . $hasil;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Form Peminjaman Baru</h4>
                    </div>
                    <div class="card-body p-4">

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" onsubmit="return validateForm()">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Peminjaman</label>
                                <select name="jenis_peminjaman" id="jenis" class="form-select" onchange="toggleForm()" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="barang">📦 Pinjam Barang (Inventaris)</option>
                                    <option value="ruangan">🏫 Booking Ruangan (Fasilitas)</option>
                                </select>
                            </div>

                            <div id="form-barang" class="d-none p-3 bg-light border rounded mb-3">
                                <label class="form-label">Pilih Barang</label>
                                <select name="barang_id" class="form-select mb-2">
                                    <?php while ($b = $listBarang->fetch_assoc()): ?>
                                        <option value="<?= $b['id'] ?>"><?= $b['nama_barang'] ?> (Sisa Stok: <?= $b['stok'] ?>)</option>
                                    <?php endwhile; ?>
                                </select>
                                <label class="form-label">Jumlah Pinjam</label>
                                <input type="number" name="jumlah" class="form-control" value="1" min="1">
                            </div>

                            <div id="form-ruangan" class="d-none p-3 bg-light border rounded mb-3">
                                <label class="form-label">Pilih Ruangan</label>
                                <select name="ruangan_id" class="form-select">
                                    <?php while ($r = $listRuang->fetch_assoc()): ?>
                                        <option value="<?= $r['id'] ?>"><?= $r['nama_ruangan'] ?> (Kapasitas: <?= $r['kapasitas'] ?>)</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tanggal Mulai</label>
                                    <input type="datetime-local" name="tgl_pinjam" id="tgl_pinjam" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tanggal Selesai</label>
                                    <input type="datetime-local" name="tgl_kembali" id="tgl_kembali" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Keperluan / Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Untuk kegiatan praktikum pengganti..." required></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane"></i> Ajukan Sekarang
                                </button>
                                <a href="dashboard.php?page=peminjaman" class="btn btn-secondary">Batal / Kembali</a>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // 1. Fungsi Ganti Tampilan (Barang vs Ruangan)
        function toggleForm() {
            var jenis = document.getElementById("jenis").value;
            var formBarang = document.getElementById("form-barang");
            var formRuangan = document.getElementById("form-ruangan");

            if (jenis === "barang") {
                formBarang.classList.remove("d-none");
                formRuangan.classList.add("d-none");
            } else if (jenis === "ruangan") {
                formBarang.classList.add("d-none");
                formRuangan.classList.remove("d-none");
            } else {
                formBarang.classList.add("d-none");
                formRuangan.classList.add("d-none");
            }
        }

        // 2. Fungsi Validasi Tanggal (Client Side)
        function validateForm() {
            var tglPinjam = document.getElementById("tgl_pinjam").value;
            var tglKembali = document.getElementById("tgl_kembali").value;

            if (tglPinjam && tglKembali) {
                // Ubah string tanggal jadi object Date JS
                var start = new Date(tglPinjam);
                var end = new Date(tglKembali);

                // Cek apakah tanggal selesai lebih kecil dari mulai?
                if (end <= start) {
                    alert("⚠️ ERROR LOGIKA:\nTanggal Selesai tidak boleh lebih awal dari Tanggal Mulai!");
                    return false; // Batalkan submit form
                }
            }
            return true; // Lanjut submit
        }
    </script>
</body>

</html>