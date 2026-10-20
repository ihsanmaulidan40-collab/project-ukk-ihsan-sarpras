<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

if (!isset($_GET['id_pelaporan'])) {
    die("ID pelaporan tidak ditemukan.");
}
$id = $_GET['id_pelaporan'];

$stmt = mysqli_prepare($koneksi, "DELETE FROM input_aspirasi WHERE id_pelaporan = ?");
mysqli_stmt_bind_param($stmt, "s", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: pelaporan_tampil.php");
    exit;
} else {
    die("Gagal menghapus data: " . mysqli_error($koneksi));
}
