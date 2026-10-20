<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

if (!isset($_GET['id_kategori'])) {
    die("ID Kategori tidak ditemukan.");
}
$id_kategori = $_GET['id_kategori'];

// Cek apakah kategori masih dipakai di aspirasi atau input_aspirasi
$cek1 = mysqli_prepare($koneksi, "SELECT id_aspirasi FROM aspirasi WHERE id_kategori = ?");
mysqli_stmt_bind_param($cek1, "s", $id_kategori);
mysqli_stmt_execute($cek1);
mysqli_stmt_store_result($cek1);
$dipakai_aspirasi = mysqli_stmt_num_rows($cek1) > 0;
mysqli_stmt_close($cek1);

$cek2 = mysqli_prepare($koneksi, "SELECT id_pelaporan FROM input_aspirasi WHERE id_kategori = ?");
mysqli_stmt_bind_param($cek2, "s", $id_kategori);
mysqli_stmt_execute($cek2);
mysqli_stmt_store_result($cek2);
$dipakai_pelaporan = mysqli_stmt_num_rows($cek2) > 0;
mysqli_stmt_close($cek2);

if ($dipakai_aspirasi || $dipakai_pelaporan) {
    die("Kategori tidak dapat dihapus karena masih digunakan pada data aspirasi/pelaporan. "
        . "<a href='kategori_tampil.php'>Kembali</a>");
}

$hapus = mysqli_prepare($koneksi, "DELETE FROM kategori WHERE id_kategori = ?");
mysqli_stmt_bind_param($hapus, "s", $id_kategori);

if (mysqli_stmt_execute($hapus)) {
    header("Location: kategori_tampil.php");
    exit;
} else {
    die("Data gagal dihapus: " . mysqli_error($koneksi));
}
