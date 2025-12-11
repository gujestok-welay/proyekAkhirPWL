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

// [MODUL 3] Tangkap Parameter
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

// PROSES AKSI
if ($aksi == 'hapus' && !empty($id)) {
    if ($ruangan->hapusRuangan($id)) {
        header("Location: dashboard.php?page=ruangan&msg=deleted");
    } else {
        header("Location: dashboard.php?page=ruangan&msg=error");
    }
    exit;
} else {
    // Jika parameter tidak lengkap
    header("Location: dashboard.php?page=ruangan");
    exit;
}
