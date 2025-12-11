-- =====================================================
-- DATABASE: UAS Lab Booking System
-- Versi: Production Ready (Synced with PHP Classes)
-- Tanggal: 12 Desember 2025
-- =====================================================

-- Hapus database jika sudah ada (HATI-HATI: ini akan menghapus semua data)
-- DROP DATABASE IF EXISTS `uas_lab_booking`;

-- Buat database baru
CREATE DATABASE IF NOT EXISTS `uas_lab_booking` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `uas_lab_booking`;

-- =====================================================
-- TABEL 1: USERS (Untuk Login & Management)
-- =====================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'MD5 Hash',
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel untuk autentikasi user';

-- =====================================================
-- TABEL 2: BARANG (Inventaris dengan Upload Gambar)
-- =====================================================
CREATE TABLE IF NOT EXISTS `barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(50) NOT NULL COMMENT 'Auto-generate: BRG-001',
  `nama_barang` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `gambar` varchar(255) NOT NULL DEFAULT 'default.jpg' COMMENT 'Nama file di folder uploads/',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_barang` (`kode_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel inventaris barang laboratorium';

-- =====================================================
-- TABEL 3: RUANGAN (Room Management dengan Upload Gambar)
-- =====================================================
CREATE TABLE IF NOT EXISTS `ruangan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_ruangan` varchar(50) NOT NULL COMMENT 'Auto-generate: LAB-001',
  `nama_ruangan` varchar(100) NOT NULL,
  `lokasi` varchar(200) NOT NULL DEFAULT 'Tidak Ditentukan',
  `kapasitas` int(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah orang',
  `fasilitas` text DEFAULT NULL COMMENT 'Deskripsi fasilitas ruangan',
  `gambar` varchar(255) NOT NULL DEFAULT 'default_ruang.jpg' COMMENT 'Nama file di folder uploads/',
  `status` enum('tersedia','tidak tersedia') NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_ruangan` (`kode_ruangan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel ruangan laboratorium';

-- =====================================================
-- TABEL 4: PEMINJAMAN (Booking Barang & Ruangan)
-- =====================================================
CREATE TABLE IF NOT EXISTS `peminjaman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'FK ke tabel users',
  `jenis_peminjaman` enum('barang','ruangan') NOT NULL,
  `barang_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel barang (NULL jika ruangan)',
  `ruangan_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel ruangan (NULL jika barang)',
  `jumlah` int(11) NOT NULL DEFAULT 1 COMMENT 'Jumlah unit barang (selalu 1 untuk ruangan)',
  `tgl_pinjam` datetime NOT NULL COMMENT 'Tanggal & waktu mulai',
  `tgl_kembali` datetime NOT NULL COMMENT 'Tanggal & waktu selesai',
  `keterangan` text DEFAULT NULL COMMENT 'Keperluan peminjaman',
  `status` enum('pending','approved','rejected','returned') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_barang_id` (`barang_id`),
  KEY `idx_ruangan_id` (`ruangan_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel transaksi peminjaman';

-- =====================================================
-- DATA SEEDING: USER ACCOUNTS
-- =====================================================

-- Admin Account (username: admin, password: admin123)
INSERT INTO `users` (`username`, `password`, `nama_lengkap`, `role`) VALUES
('admin', MD5('admin123'), 'Administrator System', 'admin');

-- Sample User Accounts
INSERT INTO `users` (`username`, `password`, `nama_lengkap`, `role`) VALUES
('dosen01', MD5('dosen123'), 'Dr. Ahmad Fauzi, M.Kom', 'user'),
('mahasiswa01', MD5('mhs123'), 'Budi Santoso', 'user');

-- =====================================================
-- DATA SEEDING: BARANG (Sample Inventory)
-- =====================================================

INSERT INTO `barang` (`kode_barang`, `nama_barang`, `deskripsi`, `stok`, `gambar`) VALUES
('BRG-001', 'Mikroskop Digital', 'Mikroskop digital dengan kamera 5MP untuk praktikum biologi', 5, 'default.jpg'),
('BRG-002', 'Laptop HP Pavilion', 'Laptop untuk keperluan praktikum programming (Core i5, RAM 8GB)', 10, 'default.jpg'),
('BRG-003', 'Proyektor LCD Epson', 'Proyektor 3000 lumens untuk presentasi dan kuliah', 3, 'default.jpg'),
('BRG-004', 'Kamera DSLR Canon EOS', 'Kamera DSLR untuk dokumentasi kegiatan laboratorium', 2, 'default.jpg'),
('BRG-005', 'Oscilloscope Digital', 'Osiloskop digital 4 channel untuk praktikum elektronika', 4, 'default.jpg');

-- =====================================================
-- DATA SEEDING: RUANGAN (Sample Rooms)
-- =====================================================

INSERT INTO `ruangan` (`kode_ruangan`, `nama_ruangan`, `lokasi`, `kapasitas`, `fasilitas`, `gambar`, `status`) VALUES
('LAB-001', 'Lab Komputer 1', 'Gedung A Lantai 2', 40, 'AC, Proyektor, 40 PC (Core i5), Whiteboard', 'default_ruang.jpg', 'tersedia'),
('LAB-002', 'Lab Komputer 2', 'Gedung A Lantai 3', 35, 'AC, Proyektor, 35 PC (Core i3), Sound System', 'default_ruang.jpg', 'tersedia'),
('LAB-003', 'Lab Jaringan Komputer', 'Gedung B Lantai 1', 30, 'AC, Router Cisco, Switch, Server Rack, 30 PC', 'default_ruang.jpg', 'tersedia'),
('LAB-004', 'Lab Multimedia', 'Gedung A Lantai 4', 25, 'AC, 25 iMac, Adobe Creative Suite, Wacom Tablet', 'default_ruang.jpg', 'tersedia'),
('LAB-005', 'Ruang Sidang Skripsi', 'Gedung C Lantai 2', 15, 'AC, Proyektor, Meja Sidang, Sound System', 'default_ruang.jpg', 'tersedia');

-- =====================================================
-- DATA SEEDING: PEMINJAMAN (Sample Transactions)
-- =====================================================

-- Sample: Peminjaman Barang (Laptop)
INSERT INTO `peminjaman` (`user_id`, `jenis_peminjaman`, `barang_id`, `ruangan_id`, `jumlah`, `tgl_pinjam`, `tgl_kembali`, `keterangan`, `status`) VALUES
(2, 'barang', 2, NULL, 2, '2025-12-10 08:00:00', '2025-12-10 12:00:00', 'Untuk praktikum Pemrograman Web', 'returned'),
(3, 'barang', 1, NULL, 1, '2025-12-11 13:00:00', '2025-12-11 15:00:00', 'Praktikum Biologi Dasar', 'approved');

-- Sample: Booking Ruangan
INSERT INTO `peminjaman` (`user_id`, `jenis_peminjaman`, `barang_id`, `ruangan_id`, `jumlah`, `tgl_pinjam`, `tgl_kembali`, `keterangan`, `status`) VALUES
(2, 'ruangan', NULL, 1, 1, '2025-12-12 10:00:00', '2025-12-12 12:00:00', 'Ujian Tengah Semester Pemrograman Web', 'approved'),
(3, 'ruangan', NULL, 5, 1, '2025-12-13 14:00:00', '2025-12-13 16:00:00', 'Sidang Proposal Skripsi', 'pending');

-- =====================================================
-- FOREIGN KEY CONSTRAINTS (Optional - untuk data integrity)
-- =====================================================

-- Uncomment jika ingin enforce referential integrity
/*
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `fk_peminjaman_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_peminjaman_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_peminjaman_ruangan` FOREIGN KEY (`ruangan_id`) REFERENCES `ruangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
*/

-- =====================================================
-- INDEXES untuk Performance Optimization
-- =====================================================

-- Index untuk kolom yang sering di-search
ALTER TABLE `barang` ADD INDEX `idx_nama_barang` (`nama_barang`);
ALTER TABLE `ruangan` ADD INDEX `idx_nama_ruangan` (`nama_ruangan`);
ALTER TABLE `peminjaman` ADD INDEX `idx_tgl_pinjam` (`tgl_pinjam`);

-- =====================================================
-- VERIFIKASI STRUKTUR TABEL
-- =====================================================

-- Uncomment untuk cek struktur tabel setelah import
-- SHOW TABLES;
-- DESCRIBE users;
-- DESCRIBE barang;
-- DESCRIBE ruangan;
-- DESCRIBE peminjaman;

-- =====================================================
-- SELESAI! Database siap digunakan.
-- =====================================================
-- Default Login:
-- Username: admin
-- Password: admin123
-- =====================================================

