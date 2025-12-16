-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 02:18 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uas_lab_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `kode_barang` varchar(50) NOT NULL COMMENT 'Auto-generate: BRG-001',
  `nama_barang` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `gambar` varchar(255) NOT NULL DEFAULT 'default.jpg' COMMENT 'Nama file di folder uploads/',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel inventaris barang laboratorium';

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `kode_barang`, `nama_barang`, `deskripsi`, `stok`, `gambar`, `created_at`) VALUES
(1, 'BRG-001', 'Mikroskop Digital', 'Mikroskop digital dengan kamera 5MP untuk praktikum', 5, '69411d8c8ce03.jpg', '2025-12-16 08:35:10'),
(2, 'BRG-002', 'Laptop HP Pavilion', 'Laptop untuk keperluan praktikum programming (Core i5, RAM 8GB)', 9, 'Laptop HP Pavilion.jpg', '2025-12-16 08:35:10'),
(3, 'BRG-003', 'Proyektor LCD Epson', 'Proyektor 3000 lumens untuk presentasi dan kuliah', 3, '693acc92ba19c.jpg', '2025-12-16 08:35:10'),
(4, 'BRG-004', 'Kamera DSLR Canon EOS', 'Kamera DSLR untuk dokumentasi kegiatan laboratorium', 2, 'Kamera DSLR Canon EOS.jpg', '2025-12-16 08:35:10'),
(5, 'BRG-005', 'Oscilloscope Digital', 'Osiloskop digital 4 channel untuk praktikum elektronika', 3, '69411deb965a0.jpg', '2025-12-16 08:35:10');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'FK ke tabel users',
  `nama_peminjam` varchar(100) NOT NULL DEFAULT 'Peminjam',
  `jenis_peminjaman` enum('barang','ruangan') NOT NULL,
  `barang_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel barang (NULL jika ruangan)',
  `ruangan_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel ruangan (NULL jika barang)',
  `jumlah` int(11) NOT NULL DEFAULT 1 COMMENT 'Jumlah unit barang (selalu 1 untuk ruangan)',
  `tgl_pinjam` datetime NOT NULL COMMENT 'Tanggal & waktu mulai',
  `tgl_kembali` datetime NOT NULL COMMENT 'Tanggal & waktu selesai',
  `keterangan` text DEFAULT NULL COMMENT 'Keperluan peminjaman',
  `status` enum('pending','approved','rejected','returned') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel transaksi peminjaman';

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `user_id`, `nama_peminjam`, `jenis_peminjaman`, `barang_id`, `ruangan_id`, `jumlah`, `tgl_pinjam`, `tgl_kembali`, `keterangan`, `status`, `created_at`) VALUES
(4, 3, 'Peminjam', 'ruangan', NULL, 5, 1, '2025-12-13 14:00:00', '2025-12-13 16:00:00', 'Sidang Proposal Skripsi', 'approved', '2025-12-16 08:35:10'),
(5, 1, 'Peminjam', 'barang', 5, NULL, 1, '2025-12-16 18:09:00', '2025-12-17 18:09:00', '-', 'approved', '2025-12-16 09:09:45'),
(6, 1, 'admin', 'barang', 5, NULL, 1, '2025-12-17 09:34:00', '2025-12-18 10:34:00', '-', 'rejected', '2025-12-16 11:36:28'),
(7, 1, 'admin', 'ruangan', NULL, 4, 1, '2025-12-17 08:00:00', '2025-12-17 09:30:00', 'untuk praktikum', 'pending', '2025-12-16 11:37:38'),
(8, 1, 'Gujestok', 'barang', 2, NULL, 1, '2025-12-17 09:00:00', '2025-12-17 10:30:00', 'Buat Tugas UAS', 'approved', '2025-12-16 12:27:00');

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `id` int(11) NOT NULL,
  `kode_ruangan` varchar(50) NOT NULL COMMENT 'Auto-generate: LAB-001',
  `nama_ruangan` varchar(100) NOT NULL,
  `lokasi` varchar(200) NOT NULL DEFAULT 'Tidak Ditentukan',
  `kapasitas` int(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah orang',
  `fasilitas` text DEFAULT NULL COMMENT 'Deskripsi fasilitas ruangan',
  `gambar` varchar(255) NOT NULL DEFAULT 'default_ruang.jpg' COMMENT 'Nama file di folder uploads/',
  `status` enum('tersedia','tidak tersedia') NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel ruangan laboratorium';

--
-- Dumping data for table `ruangan`
--

INSERT INTO `ruangan` (`id`, `kode_ruangan`, `nama_ruangan`, `lokasi`, `kapasitas`, `fasilitas`, `gambar`, `status`, `created_at`) VALUES
(1, 'LAB-001', 'Lab Komputer 1', 'Gedung A Lantai 2', 40, 'AC, Proyektor, 40 PC (Core i5), Whiteboard', '693adb50a4b51.jpg', 'tersedia', '2025-12-16 08:35:10'),
(2, 'LAB-002', 'Lab Komputer 2', 'Gedung A Lantai 3', 35, 'AC, Proyektor, 35 PC (Core i3), Sound System', '693adb1f24998.jpg', 'tersedia', '2025-12-16 08:35:10'),
(3, 'LAB-003', 'Lab Jaringan Komputer', 'Gedung B Lantai 1', 30, 'AC, Router Cisco, Switch, Server Rack, 30 PC', '69411fa800c1b.jpg', 'tersedia', '2025-12-16 08:35:10'),
(4, 'LAB-004', 'Lab Multimedia', 'Gedung A Lantai 4', 25, 'AC, 25 iMac, Adobe Creative Suite, Wacom Tablet', '69411eea2e0dc.jpg', 'tersedia', '2025-12-16 08:35:10'),
(5, 'LAB-005', 'Ruang Sidang Skripsi', 'Gedung C Lantai 2', 15, 'AC, Proyektor, Meja Sidang, Sound System', '69411eda92ebe.jpg', 'tersedia', '2025-12-16 08:35:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'MD5 Hash',
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel untuk autentikasi user';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'Administrator System', 'admin', '2025-12-16 08:35:10'),
(2, 'dosen01', 'd5bbfb47ac3160c31fa8c247827115aa', 'Dr. Ahmad Fauzi, M.Kom', 'user', '2025-12-16 08:35:10'),
(3, 'mahasiswa01', '39f55dd65ead9c938fa93a765983bff0', 'Budi Santoso', 'user', '2025-12-16 08:35:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_barang` (`kode_barang`),
  ADD KEY `idx_nama_barang` (`nama_barang`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_barang_id` (`barang_id`),
  ADD KEY `idx_ruangan_id` (`ruangan_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tgl_pinjam` (`tgl_pinjam`);

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_ruangan` (`kode_ruangan`),
  ADD KEY `idx_nama_ruangan` (`nama_ruangan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
