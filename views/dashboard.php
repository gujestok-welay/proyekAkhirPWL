<?php
session_start();
// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: ../index.php");
    exit;
}

include_once '../config/Database.php';
include_once '../classes/Barang.php';
// Pastikan nama file sesuai (Ruang.php atau Ruangan.php)
// Di sini saya pakai Ruangan.php sesuai saran terakhir
include_once '../classes/Ruangan.php';
include_once '../classes/Peminjaman.php';

$database = new Database();
$db = $database->getConnection();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lab System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }

        /* Sidebar Styling */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background: #212529;
            padding-top: 20px;
            color: white;
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 16px;
            color: #adb5bd;
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            color: #fff;
            background-color: #0d6efd;
            /* Bootstrap Primary Color */
        }

        .sidebar i {
            width: 25px;
            /* Lebar icon tetap biar teks rata */
        }

        /* Content Styling */
        .content {
            margin-left: 250px;
            /* Geser konten ke kanan sebesar lebar sidebar */
            padding: 30px;
        }

        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .table th {
            background-color: #e9ecef;
            color: black;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-flask"></i> LAB SYSTEM</h4>
        </div>

        <?php $p = isset($_GET['page']) ? $_GET['page'] : 'home'; ?>

        <a href="dashboard.php?page=home" class="<?= $p == 'home' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="dashboard.php?page=barang" class="<?= $p == 'barang' ? 'active' : '' ?>">
            <i class="fas fa-box"></i> Data Barang
        </a>
        <a href="dashboard.php?page=ruangan" class="<?= $p == 'ruangan' ? 'active' : '' ?>">
            <i class="fas fa-door-open"></i> Data Ruangan
        </a>
        <a href="dashboard.php?page=peminjaman" class="<?= $p == 'peminjaman' ? 'active' : '' ?>">
            <i class="fas fa-file-signature"></i> Peminjaman
        </a>

        <a href="../logout.php" class="mt-5 text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="content">
        <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'home';

        switch ($page) {
            case 'home':
                // [MODUL 4] Hitung Statistik Dashboard
                $barangObj = new Barang($db);
                $ruangObj = new Ruangan($db);
                $pinjamObj = new Peminjaman($db);

                $totalBarang = $barangObj->hitungJumlah();
                $totalRuangan = $ruangObj->hitungJumlah();
                $totalPeminjaman = $pinjamObj->hitungJumlah();
        ?>
                <h2 class="mb-4">Dashboard Utama</h2>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-primary">
                            <h4 class="alert-heading"><i class="fas fa-user-circle"></i> Selamat Datang, <?php echo $_SESSION['nama']; ?>!</h4>
                            <p>Anda login sebagai <strong><?php echo ucfirst($_SESSION['role']); ?></strong>. Gunakan menu di samping untuk mengelola laboratorium.</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-primary text-white p-3">
                            <h3><i class="fas fa-box-open"></i> Barang</h3>
                            <p>Manajemen Inventaris</p>
                            <hr class="bg-white">
                            <h2 class="mb-0"><i class="fas fa-database"></i> <?= $totalBarang ?> Item</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white p-3">
                            <h3><i class="fas fa-building"></i> Ruangan</h3>
                            <p>Manajemen Fasilitas</p>
                            <hr class="bg-white">
                            <h2 class="mb-0"><i class="fas fa-door-open"></i> <?= $totalRuangan ?> Ruang</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-dark p-3">
                            <h3><i class="fas fa-clipboard-list"></i> Transaksi</h3>
                            <p>Peminjaman & Booking</p>
                            <hr>
                            <h2 class="mb-0"><i class="fas fa-history"></i> <?= $totalPeminjaman ?> Record</h2>
                        </div>
                    </div>
                </div>
            <?php
                break;

            case 'barang':
                $barangObj = new Barang($db);

                // [MODUL 4 - Latihan 7] Logic Pencarian
                $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

                if (!empty($keyword)) {
                    // Jika ada keyword, panggil method cariBarang()
                    $dataBarang = $barangObj->cariBarang($keyword);
                } else {
                    // Jika tidak ada keyword, tampilkan semua data
                    $dataBarang = $barangObj->tampilSemua();
                }
            ?>
                <div class="d-flex justify-content-between mb-4">
                    <h2><i class="fas fa-box"></i> Inventaris Barang</h2>
                    <a href="tambah_barang.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Barang</a>
                </div>

                <!-- [MODUL 4 - Latihan 7] Form Pencarian -->
                <div class="card mb-3 p-3">
                    <form method="GET" action="dashboard.php" class="row g-3 align-items-center">
                        <input type="hidden" name="page" value="barang">
                        <div class="col-md-8">
                            <label class="form-label"><i class="fas fa-search"></i> Cari Barang</label>
                            <input type="text" name="keyword" class="form-control"
                                placeholder="Ketik Nama Barang atau Kode..."
                                value="<?= htmlspecialchars($keyword) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <a href="dashboard.php?page=barang" class="btn btn-secondary w-100">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </form>

                    <?php if (!empty($keyword)): ?>
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-info-circle"></i> Hasil pencarian untuk: <strong>"<?= htmlspecialchars($keyword) ?>"</strong>
                            (Ditemukan: <strong><?= $dataBarang->num_rows ?> data</strong>)
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card p-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Kode & Nama</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($row = $dataBarang->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <?php
                                            $img = $row['gambar'] ? $row['gambar'] : 'default.jpg';
                                            // Cek jika file ada di folder uploads, jika tidak pakai placeholder
                                            $imgPath = "../uploads/" . $img;
                                            if (!file_exists($imgPath)) {
                                                $img = "default.jpg";
                                            }
                                            ?>
                                            <img src="../uploads/<?= $img ?>" width="60" height="60" style="object-fit: cover; border-radius: 5px;">
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary mb-1"><?= $row['kode_barang'] ?></span><br>
                                            <strong><?= $row['nama_barang'] ?></strong><br>
                                            <small class="text-muted"><?= $row['deskripsi'] ?></small>
                                        </td>
                                        <td>
                                            <?php if ($row['stok'] > 0): ?>
                                                <span class="badge bg-info text-dark"><?= $row['stok'] ?> Unit</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Habis</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="edit_barang.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>

                                            <a href="aksi_barang.php?aksi=hapus&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php
                break;

            case 'ruangan':
                $ruangObj = new Ruangan($db);
                $dataRuang = $ruangObj->tampilSemua();
            ?>
                <div class="d-flex justify-content-between mb-4">
                    <h2><i class="fas fa-door-open"></i> Daftar Ruangan</h2>
                    <a href="tambah_ruangan.php" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Ruangan</a>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-<?= $_GET['msg'] == 'added' || $_GET['msg'] == 'updated' ? 'success' : ($_GET['msg'] == 'deleted' ? 'info' : 'danger') ?> alert-dismissible fade show">
                        <?php
                        if ($_GET['msg'] == 'added') echo '<i class="fas fa-check-circle"></i> Ruangan berhasil ditambahkan!';
                        elseif ($_GET['msg'] == 'updated') echo '<i class="fas fa-check-circle"></i> Ruangan berhasil diupdate!';
                        elseif ($_GET['msg'] == 'deleted') echo '<i class="fas fa-trash"></i> Ruangan berhasil dihapus!';
                        else echo '<i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan!';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card p-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Kode & Nama</th>
                                    <th>Lokasi</th>
                                    <th>Kapasitas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($row = $dataRuang->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <?php
                                            // Handle backward compatibility jika kolom 'gambar' belum ada
                                            $img = isset($row['gambar']) && $row['gambar'] ? $row['gambar'] : 'default.jpg';
                                            $imgPath = "../uploads/" . $img;
                                            if (!file_exists($imgPath)) {
                                                $img = "default.jpg";
                                            }
                                            ?>
                                            <img src="../uploads/<?= $img ?>" width="60" height="60" style="object-fit: cover; border-radius: 5px;">
                                        </td>
                                        <td>
                                            <?php
                                            // Handle kolom kode yang mungkin bernama 'kode_ruang' atau 'kode_ruangan'
                                            $kode = $row['kode_ruangan'] ?? $row['kode_ruang'] ?? 'N/A';
                                            $fasilitas = $row['fasilitas'] ?? 'Tidak ada informasi';
                                            $lokasi = $row['lokasi'] ?? 'Tidak ditentukan';
                                            ?>
                                            <span class="badge bg-secondary mb-1"><?= $kode ?></span><br>
                                            <strong><?= $row['nama_ruangan'] ?></strong><br>
                                            <small class="text-muted"><i class="fas fa-info-circle"></i> <?= $fasilitas ?></small>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-danger"></i> <?= $lokasi ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark"><i class="fas fa-users"></i> <?= $row['kapasitas'] ?> Orang</span>
                                        </td>
                                        <td>
                                            <a href="edit_ruangan.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <a href="aksi_ruangan.php?aksi=hapus&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus ruangan ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php
                break;

            case 'peminjaman':
                $pinjamObj = new Peminjaman($db);
                $riwayat = $pinjamObj->tampilRiwayat();
            ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-history"></i> Riwayat Transaksi</h2>
                    <div>
                        <a href="cetak_laporan.php" target="_blank" class="btn btn-secondary me-2">
                            <i class="fas fa-print"></i> Cetak Laporan
                        </a>

                        <a href="tambah_peminjaman.php" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Pinjam Baru
                        </a>
                    </div>
                </div>

                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'sukses'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Berhasil!</strong> Data peminjaman telah disimpan / diperbarui.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card p-3">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Tipe</th>
                                <th>Item / Ruang</th>
                                <th>Status</th>
                                <th>Aksi Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $riwayat->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><i class="fas fa-user text-secondary"></i> <?= $row['nama_lengkap'] ?></td>
                                    <td>
                                        <?php if ($row['jenis_peminjaman'] == 'barang'): ?>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-box"></i> Barang</span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark"><i class="fas fa-building"></i> Ruangan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong>
                                            <?php
                                            if ($row['jenis_peminjaman'] == 'barang') {
                                                echo $row['nama_barang'] . " <span class='text-muted'>(" . $row['jumlah'] . " Unit)</span>";
                                            } else {
                                                echo $row['nama_ruangan'];
                                            }
                                            ?>
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="far fa-clock"></i> <?= date('d M Y H:i', strtotime($row['tgl_pinjam'])) ?> <br>
                                            s/d <?= date('d M Y H:i', strtotime($row['tgl_kembali'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $s = $row['status'];
                                        $badge = 'bg-secondary';
                                        if ($s == 'pending') $badge = 'bg-warning text-dark';
                                        if ($s == 'approved') $badge = 'bg-primary';
                                        if ($s == 'returned') $badge = 'bg-success';
                                        if ($s == 'rejected') $badge = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $badge ?>"><?= strtoupper($s) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($s == 'pending'): ?>
                                            <a href="proses_aksi.php?id=<?= $row['id'] ?>&aksi=approved" class="btn btn-sm btn-outline-primary" onclick="return confirm('Approve?')">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="proses_aksi.php?id=<?= $row['id'] ?>&aksi=rejected" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tolak?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php elseif ($s == 'approved'): ?>
                                            <a href="proses_aksi.php?id=<?= $row['id'] ?>&aksi=returned" class="btn btn-sm btn-success" onclick="return confirm('Barang kembali?')">
                                                <i class="fas fa-undo"></i> Selesai
                                            </a>
                                        <?php else: ?>
                                            <i class="fas fa-check-circle text-success"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
        <?php
                break;

            default:
                echo "<div class='alert alert-warning'>Halaman tidak ditemukan!</div>";
                break;
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>