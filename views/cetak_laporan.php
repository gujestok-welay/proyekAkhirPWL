<?php
session_start();
// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: ../index.php");
    exit;
}

include_once '../config/Database.php';
include_once '../classes/Peminjaman.php';

$database = new Database();
$db = $database->getConnection();

$pinjamObj = new Peminjaman($db);
$riwayat = $pinjamObj->tampilRiwayat();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Laporan Peminjaman Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
        }

        .kop-surat {
            border-bottom: 3px double black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .ttd-area {
            margin-top: 50px;
            float: right;
            text-align: center;
            width: 200px;
        }

        /* Trik CSS: Sembunyikan tombol saat dicetak */
        @media print {
            .no-print {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .container {
                padding: 0 10px;
            }

            h3 {
                font-size: 1rem;
            }

            h5 {
                font-size: 0.9rem;
            }

            small {
                font-size: 10px;
            }

            .table {
                font-size: 11px;
            }

            .table th,
            .table td {
                padding: 6px 4px !important;
            }

            .no-print .btn {
                width: 100%;
                margin-bottom: 8px;
                font-size: 12px;
                padding: 8px 12px;
            }

            .no-print {
                margin-bottom: 15px;
            }

            .ttd-area {
                width: 150px;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container mt-4">

        <div class="no-print mb-3">
            <a href="dashboard.php?page=peminjaman" class="btn btn-secondary btn-sm">← Kembali ke Dashboard</a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">🖨️ Cetak Lagi</button>
        </div>

        <div class="text-center kop-surat">
            <h3 class="mb-0">UNIVERSITAS TEKNOLOGI MASA DEPAN</h3>
            <h5 class="mb-0">UPT LABORATORIUM KOMPUTER</h5>
            <small>Jl. Teknologi No. 1, Kota Coding. Telp: (021) 12345678</small>
        </div>

        <h4 class="text-center mb-4">LAPORAN PEMINJAMAN & PENGGUNAAN LAB</h4>
        <p>Dicetak pada: <?= date('d F Y, H:i') ?> WIB</p>

        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Peminjam</th>
                    <th>Jenis</th>
                    <th>Item / Ruangan</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                while ($row = $riwayat->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= !empty($row['nama_peminjam']) ? htmlspecialchars($row['nama_peminjam']) : htmlspecialchars($row['nama_lengkap']) ?>
                        </td>
                        <td><?= strtoupper($row['jenis_peminjaman']) ?></td>
                        <td>
                            <?php
                            if ($row['jenis_peminjaman'] == 'barang') {
                                echo $row['nama_barang'] . " (" . $row['jumlah'] . ")";
                            } else {
                                echo $row['nama_ruangan'];
                            }
                            ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['tgl_pinjam'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['tgl_kembali'])) ?></td>
                        <td><?= strtoupper($row['status']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="ttd-area">
            <p>Mengetahui,<br>Kepala Laboratorium</p>
            <br><br><br>
            <p><strong>( ____________________ )</strong><br>NIP. 19800101 202301 1 001</p>
        </div>

    </div>

</body>

</html>