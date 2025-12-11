<?php
session_start();
include_once '../config/Database.php';
include_once '../classes/Peminjaman.php';

$database = new Database();
$db = $database->getConnection();
$pinjam = new Peminjaman($db);

// Cek parameter di URL
if (isset($_GET['id']) && isset($_GET['aksi'])) {
    $id = $_GET['id'];
    $aksi = $_GET['aksi'];

    // Panggil fungsi updateStatus yang baru kita buat
    if ($pinjam->updateStatus($id, $aksi)) {
        // Balik lagi ke dashboard
        header("Location: dashboard.php?page=peminjaman&msg=sukses");
    } else {
        echo "Gagal update status.";
    }
}
?>