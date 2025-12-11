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
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white p-3">
                            <h3><i class="fas fa-building"></i> Ruangan</h3>
                            <p>Manajemen Fasilitas</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-dark p-3">
                            <h3><i class="fas fa-clipboard-list"></i> Transaksi</h3>
                            <p>Peminjaman & Booking</p>
                        </div>
                    </div>
                </div>
            <?php
                break;

            case 'barang':
                $barangObj = new Barang($db);
                $dataBarang = $barangObj->tampilSemua();
            ?>
                <h2 class="mb-4"><i class="fas fa-box"></i> Inventaris Barang</h2>
                <div class="card p-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($row = $dataBarang->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><span class="badge bg-secondary"><?= $row['kode_barang'] ?></span></td>
                                        <td>
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
                                            <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
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
                <h2 class="mb-4"><i class="fas fa-door-open"></i> Daftar Ruangan</h2>
                <div class="card p-3">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Ruangan</th>
                                <th>Lokasi</th>
                                <th>Kapasitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $dataRuang->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><span class="badge bg-secondary"><?= $row['kode_ruang'] ?></span></td>
                                    <td><?= $row['nama_ruangan'] ?></td>
                                    <td><i class="fas fa-map-marker-alt text-danger"></i> <?= $row['lokasi'] ?></td>
                                    <td><?= $row['kapasitas'] ?> Orang</td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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