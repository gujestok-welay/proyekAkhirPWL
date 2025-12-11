<?php
session_start();
include_once '../config/Database.php';
include_once '../classes/Barang.php';

$database = new Database();
$db = $database->getConnection();
$barang = new Barang($db);

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($barang->hapusBarang($id)) {
        header("Location: dashboard.php?page=barang&msg=deleted");
    } else {
        echo "Gagal menghapus data.";
    }
}
