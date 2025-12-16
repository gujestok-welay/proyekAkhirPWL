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

    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background: #212529;
            padding-top: 20px;
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 0 15px;
        }

        .sidebar-header h4 {
            font-size: 1.2rem;
        }

        .sidebar a {
            padding: 12px 20px;
            text-decoration: none;
            font-size: 14px;
            color: #adb5bd;
            display: block;
            transition: 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar a:hover,
        .sidebar a.active {
            color: #fff;
            background-color: #0d6efd;
            border-left-color: #fff;
        }

        .sidebar i {
            width: 20px;
            margin-right: 10px;
        }

        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: #212529;
            color: white;
            border: none;
            padding: 8px 12px;
            font-size: 20px;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
        }

        .sidebar-toggle:active {
            background: #0d6efd;
            transform: scale(0.92);
        }

        .sidebar-toggle:hover {
            background: #0d6efd;
        }

        .content {
            margin-left: 250px;
            padding: 20px 15px;
            transition: margin-left 0.3s ease;
        }

        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .table {
            font-size: 13px;
        }

        .table th {
            background-color: #e9ecef;
            color: black;
            padding: 10px 8px !important;
        }

        .table td {
            padding: 10px 8px !important;
            vertical-align: middle;
        }

        .btn-sm {
            padding: 5px 8px;
            font-size: 12px;
        }

        .action-buttons {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            white-space: nowrap;
        }

        .transaction-card {
            border-left: 4px solid #0d6efd;
            padding: 12px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .transaction-card.pending {
            border-left-color: #ffc107;
        }

        .transaction-card.approved {
            border-left-color: #0d6efd;
        }

        .transaction-card.returned {
            border-left-color: #28a745;
        }

        .transaction-card.rejected {
            border-left-color: #dc3545;
        }

        .transaction-card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 8px;
        }

        .transaction-card-title {
            font-weight: bold;
            font-size: 14px;
        }

        .transaction-card-status {
            font-size: 12px;
        }

        .transaction-card-detail {
            font-size: 13px;
            color: #666;
            margin: 4px 0;
        }

        .transaction-card-actions {
            margin-top: 8px;
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .transaction-card-actions .btn {
            padding: 4px 8px;
            font-size: 11px;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                width: 100%;
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
                box-shadow: 2px 0 15px rgba(0, 0, 0, 0.3);
            }

            .sidebar-toggle {
                display: block;
            }

            .content {
                margin-left: 0;
                padding: 75px 15px 20px 15px;
            }

            .sidebar-header {
                margin-bottom: 20px;
            }

            .sidebar-header h4 {
                font-size: 1rem;
            }

            .sidebar a {
                padding: 10px 15px;
                font-size: 13px;
            }

            .table {
                font-size: 12px;
            }

            .table th,
            .table td {
                padding: 8px 6px !important;
            }

            .btn {
                padding: 8px 12px;
                font-size: 12px;
            }
        }

        @media (max-width: 767.98px) {
            .action-buttons {
                gap: 4px;
            }

            .action-buttons .btn {
                flex: 1;
                min-width: 45px;
                padding: 6px 4px;
                font-size: 10px;
            }
        }

        @media (max-width: 575.98px) {
            .content {
                padding: 75px 12px 15px 12px;
            }

            h1 {
                font-size: 1.4rem;
            }

            h2 {
                font-size: 1.15rem;
                margin-bottom: 15px;
            }

            h3 {
                font-size: 0.95rem;
            }

            h4 {
                font-size: 0.9rem;
            }

            .card p {
                font-size: 12px;
            }

            .card h3 {
                font-size: 0.9rem;
                margin-bottom: 5px;
            }

            .card h2 {
                font-size: 1.3rem;
            }

            .table-responsive {
                font-size: 11px;
            }

            .btn-sm {
                padding: 5px 8px;
                font-size: 11px;
            }

            .btn {
                padding: 8px 12px;
                font-size: 12px !important;
            }

            .alert {
                padding: 10px 12px;
                margin-bottom: 15px;
                font-size: 13px;
            }

            .alert-heading {
                font-size: 0.95rem;
                margin-bottom: 6px;
            }

            .modal .modal-dialog {
                margin: 10px auto;
            }

            .table-hide-mobile {
                display: none;
            }
        }

        .table-responsive {
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 991.98px) {
            .table-hide-mobile {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <!-- Hamburger Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Menu">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Modal Konfirmasi Reusable -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title confirm-title">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="confirm-message">Apakah Anda yakin?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary btn-confirm">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

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
                            <h4 class="alert-heading"><i class="fas fa-user-circle"></i> Selamat Datang,
                                <?php echo $_SESSION['nama']; ?>!
                            </h4>
                            <p>Anda login sebagai <strong><?php echo ucfirst($_SESSION['role']); ?></strong>. Gunakan menu di
                                samping untuk mengelola laboratorium.</p>
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
                            <input type="text" name="keyword" class="form-control" placeholder="Ketik Nama Barang atau Kode..."
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
                            <i class="fas fa-info-circle"></i> Hasil pencarian untuk:
                            <strong>"<?= htmlspecialchars($keyword) ?>"</strong>
                            (Ditemukan: <strong><?= $dataBarang->num_rows ?> data</strong>)
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card p-3">
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="min-width: 30px;">No</th>
                                        <th style="min-width: 50px;">Gambar</th>
                                        <th style="min-width: 150px;">Kode & Nama</th>
                                        <th style="min-width: 60px;">Stok</th>
                                        <th style="min-width: 80px;">Aksi</th>
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
                                                <img src="../uploads/<?= $img ?>" width="60" height="60"
                                                    style="object-fit: cover; border-radius: 5px;">
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
                                                <a href="edit_barang.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i
                                                        class="fas fa-edit"></i></a>

                                                <button type="button" class="btn btn-sm btn-danger" onclick="
                                                showConfirm({
                                                    title: 'Hapus Barang',
                                                    message: 'Yakin ingin menghapus: <?= htmlspecialchars($row['nama_barang']) ?>?\nTindakan tidak dapat dibatalkan.',
                                                    confirmText: 'Ya, Hapus',
                                                    variant: 'danger',
                                                    onConfirm: function(){ window.location='aksi_barang.php?aksi=hapus&id=<?= $row['id'] ?>'; }
                                                });
                                            ">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
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
                    <div
                        class="alert alert-<?= $_GET['msg'] == 'added' || $_GET['msg'] == 'updated' ? 'success' : ($_GET['msg'] == 'deleted' ? 'info' : 'danger') ?> alert-dismissible fade show">
                        <?php
                        if ($_GET['msg'] == 'added')
                            echo '<i class="fas fa-check-circle"></i> Ruangan berhasil ditambahkan!';
                        elseif ($_GET['msg'] == 'updated')
                            echo '<i class="fas fa-check-circle"></i> Ruangan berhasil diupdate!';
                        elseif ($_GET['msg'] == 'deleted')
                            echo '<i class="fas fa-trash"></i> Ruangan berhasil dihapus!';
                        else
                            echo '<i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan!';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card p-3">
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="min-width: 30px;">No</th>
                                        <th style="min-width: 50px;">Gambar</th>
                                        <th style="min-width: 150px;">Kode & Nama</th>
                                        <th style="min-width: 100px;">Lokasi</th>
                                        <th style="min-width: 80px;">Kapasitas</th>
                                        <th style="min-width: 80px;">Aksi</th>
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
                                                <img src="../uploads/<?= $img ?>" width="60" height="60"
                                                    style="object-fit: cover; border-radius: 5px;">
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
                                                <small class="text-muted"><i class="fas fa-info-circle"></i>
                                                    <?= $fasilitas ?></small>
                                            </td>
                                            <td>
                                                <i class="fas fa-map-marker-alt text-danger"></i> <?= $lokasi ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-dark"><i class="fas fa-users"></i>
                                                    <?= $row['kapasitas'] ?> Orang</span>
                                            </td>
                                            <td>
                                                <a href="edit_ruangan.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <button type="button" class="btn btn-sm btn-danger" onclick="
                                                showConfirm({
                                                    title: 'Hapus Ruangan',
                                                    message: 'Yakin ingin menghapus: <?= htmlspecialchars($row['nama_ruangan']) ?>?\nTindakan tidak dapat dibatalkan.',
                                                    confirmText: 'Ya, Hapus',
                                                    variant: 'danger',
                                                    onConfirm: function(){ window.location='aksi_ruangan.php?aksi=hapus&id=<?= $row['id'] ?>'; }
                                                });
                                            ">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
                break;

            case 'peminjaman':
                $pinjamObj = new Peminjaman($db);
                $riwayat = $pinjamObj->tampilRiwayat();
                ?>

                <div class="mb-4">
                    <h2 class="mb-3"><i class="fas fa-history"></i> Riwayat Transaksi</h2>
                    <div class="row g-2">
                        <div class="col-12 col-sm-6">
                            <a href="cetak_laporan.php" target="_blank" class="btn btn-secondary w-100">
                                <i class="fas fa-print"></i> Cetak Laporan
                            </a>
                        </div>
                        <div class="col-12 col-sm-6">
                            <a href="tambah_peminjaman.php" class="btn btn-success w-100">
                                <i class="fas fa-plus-circle"></i> Pinjam Baru
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'sukses'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Berhasil!</strong> Data peminjaman telah disimpan / diperbarui.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Desktop Table View (Hidden on mobile) -->
                <div class="card p-3 d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 25px;">No</th>
                                    <th style="min-width: 120px;">Peminjam</th>
                                    <th style="min-width: 60px;">Tipe</th>
                                    <th style="min-width: 150px;">Item / Ruang</th>
                                    <th style="min-width: 80px;">Status</th>
                                    <th style="min-width: 100px;">Aksi Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($row = $riwayat->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><i class="fas fa-user text-secondary"></i>
                                            <?= !empty($row['nama_peminjam']) ? htmlspecialchars($row['nama_peminjam']) : htmlspecialchars($row['nama_lengkap']) ?>
                                        </td>
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
                                                <i class="far fa-clock"></i> <?= date('d M Y H:i', strtotime($row['tgl_pinjam'])) ?>
                                                <br>
                                                s/d <?= date('d M Y H:i', strtotime($row['tgl_kembali'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                            $s = $row['status'];
                                            $badge = 'bg-secondary';
                                            if ($s == 'pending')
                                                $badge = 'bg-warning text-dark';
                                            if ($s == 'approved')
                                                $badge = 'bg-primary';
                                            if ($s == 'returned')
                                                $badge = 'bg-success';
                                            if ($s == 'rejected')
                                                $badge = 'bg-danger';
                                            ?>
                                            <span class="badge <?= $badge ?>"><?= strtoupper($s) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($s == 'pending'): ?>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="
                                                showConfirm({
                                                    title: 'Approve Peminjaman',
                                                    message: 'Setujui peminjaman ini?',
                                                    confirmText: 'Ya, Approve',
                                                    variant: 'primary',
                                                    onConfirm: function(){ window.location='proses_aksi.php?id=<?= $row['id'] ?>\u0026aksi=approved'; }
                                                });
                                            ">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="
                                                showConfirm({
                                                    title: 'Tolak Peminjaman',
                                                    message: 'Tolak peminjaman ini?',
                                                    confirmText: 'Ya, Tolak',
                                                    variant: 'danger',
                                                    onConfirm: function(){ window.location='proses_aksi.php?id=<?= $row['id'] ?>\u0026aksi=rejected'; }
                                                });
                                            ">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php elseif ($s == 'approved'): ?>
                                                <button type="button" class="btn btn-sm btn-success" onclick="
                                                showConfirm({
                                                    title: 'Selesaikan Peminjaman',
                                                    message: 'Tandai sebagai kembali untuk: <?= htmlspecialchars($row['jenis_peminjaman'] == 'barang' ? ($row['nama_barang'] . " (" . $row['jumlah'] . " Unit)") : $row['nama_ruangan']) ?>?\nPeriode: <?= date('d M Y H:i', strtotime($row['tgl_pinjam'])) ?> s/d <?= date('d M Y H:i', strtotime($row['tgl_kembali'])) ?>',
                                                    confirmText: 'Ya, Selesai',
                                                    variant: 'success',
                                                    onConfirm: function(){ window.location='proses_aksi.php?id=<?= $row['id'] ?>\u0026aksi=returned'; }
                                                });
                                            ">
                                                    <i class="fas fa-undo"></i> Selesai
                                                </button>
                                            <?php else: ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View (Hidden on desktop) -->
                <div class="d-md-none">
                    <?php
                    // Reset result pointer untuk mobile view
                    $riwayat = $pinjamObj->tampilRiwayat();
                    $no = 1;
                    while ($row = $riwayat->fetch_assoc()):
                        $s = $row['status'];
                        $statusClass = 'pending';
                        if ($s == 'approved')
                            $statusClass = 'approved';
                        elseif ($s == 'returned')
                            $statusClass = 'returned';
                        elseif ($s == 'rejected')
                            $statusClass = 'rejected';
                        ?>
                        <div class="transaction-card <?= $statusClass ?>">
                            <div class="transaction-card-header">
                                <div class="transaction-card-title">
                                    <?php
                                    if ($row['jenis_peminjaman'] == 'barang') {
                                        echo $row['nama_barang'] . " (" . $row['jumlah'] . " Unit)";
                                    } else {
                                        echo $row['nama_ruangan'];
                                    }
                                    ?>
                                </div>
                                <?php
                                $badge = 'bg-secondary';
                                if ($s == 'pending')
                                    $badge = 'bg-warning text-dark';
                                if ($s == 'approved')
                                    $badge = 'bg-primary';
                                if ($s == 'returned')
                                    $badge = 'bg-success';
                                if ($s == 'rejected')
                                    $badge = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge ?> transaction-card-status"><?= strtoupper($s) ?></span>
                            </div>
                            <div class="transaction-card-detail">
                                <i class="fas fa-user text-secondary"></i>
                                <?= !empty($row['nama_peminjam']) ? htmlspecialchars($row['nama_peminjam']) : htmlspecialchars($row['nama_lengkap']) ?>
                            </div>
                            <div class="transaction-card-detail">
                                <i class="fas fa-calendar"></i>
                                <?= date('d M Y H:i', strtotime($row['tgl_pinjam'])) ?>
                            </div>
                            <div class="transaction-card-detail">
                                <i class="fas fa-arrow-right"></i>
                                <?= date('d M Y H:i', strtotime($row['tgl_kembali'])) ?>
                            </div>
                            <div class="transaction-card-actions">
                                <?php if ($s == 'pending'): ?>
                                    <button type="button" class="btn btn-outline-primary" onclick="
                                        showConfirm({
                                            title: 'Approve',
                                            message: 'Setujui peminjaman ini?',
                                            confirmText: 'Ya',
                                            variant: 'primary',
                                            onConfirm: function(){ window.location='proses_aksi.php?id=<?= $row['id'] ?>\u0026aksi=approved'; }
                                        });
                                    ">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" onclick="
                                        showConfirm({
                                            title: 'Tolak',
                                            message: 'Tolak peminjaman ini?',
                                            confirmText: 'Ya',
                                            variant: 'danger',
                                            onConfirm: function(){ window.location='proses_aksi.php?id=<?= $row['id'] ?>\u0026aksi=rejected'; }
                                        });
                                    ">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                <?php elseif ($s == 'approved'): ?>
                                    <button type="button" class="btn btn-success" onclick="
                                        showConfirm({
                                            title: 'Selesaikan',
                                            message: 'Tandai sebagai kembali?',
                                            confirmText: 'Ya',
                                            variant: 'success',
                                            onConfirm: function(){ window.location='proses_aksi.php?id=<?= $row['id'] ?>\u0026aksi=returned'; }
                                        });
                                    ">
                                        <i class="fas fa-undo"></i> Selesai
                                    </button>
                                <?php else: ?>
                                    <span class="text-success"><i class="fas fa-check-circle"></i> Selesai</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
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
    <script src="../assets/js/script.js"></script>

    <!-- Mobile Sidebar Toggle -->
    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function (e) {
                e.preventDefault();
                sidebar.classList.toggle('show');
            });

            document.querySelectorAll('.sidebar a').forEach(link => {
                link.addEventListener('click', function () {
                    if (window.innerWidth <= 991.98) {
                        sidebar.classList.remove('show');
                    }
                });
            });

            document.addEventListener('click', function (event) {
                if (window.innerWidth <= 991.98) {
                    if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
        }
    </script>
</body>

</html>