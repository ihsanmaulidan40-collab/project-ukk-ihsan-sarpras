<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

if (!isset($_GET['username'])) {
    die("Username tidak ditemukan.");
}
$username = $_GET['username'];

$hapus = mysqli_prepare($koneksi, "DELETE FROM petugas WHERE username = ?");
mysqli_stmt_bind_param($hapus, "s", $username);

if (mysqli_stmt_execute($hapus)) {
    header("Location: petugas_tampil.php");
    exit;
} else {
    die("Gagal menghapus data: " . mysqli_error($koneksi));
}
