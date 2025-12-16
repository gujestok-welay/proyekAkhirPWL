-- =====================================================
-- SQL MIGRATION: Upgrade Tabel Ruangan
-- Menambahkan kolom 'gambar' dan 'lokasi' 
-- untuk fitur CRUD lengkap dengan upload image
-- =====================================================

-- Tambah kolom 'lokasi' jika belum ada
ALTER TABLE `ruangan` 
ADD COLUMN IF NOT EXISTS `lokasi` VARCHAR(200) NOT NULL DEFAULT 'Tidak Ditentukan' AFTER `nama_ruangan`;

-- Tambah kolom 'gambar' jika belum ada
ALTER TABLE `ruangan` 
ADD COLUMN IF NOT EXISTS `gambar` VARCHAR(255) NOT NULL DEFAULT 'default.jpg' AFTER `fasilitas`;

-- Update kode_ruangan menjadi kode_ruangan (konsistensi penamaan)
-- Jika struktur existing masih pakai 'kode_ruang', run query ini:
-- ALTER TABLE `ruangan` CHANGE `kode_ruang` `kode_ruangan` VARCHAR(50) NOT NULL;

-- Verifikasi struktur tabel
DESCRIBE `ruangan`;
