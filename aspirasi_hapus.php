<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/upload.php';

if (!isset($_GET['id_aspirasi'])) {
    die("ID aspirasi tidak ditemukan.");
}
$id = $_GET['id_aspirasi'];

// Ambil nama file foto dulu, supaya filenya ikut dihapus dari disk
$ambil = mysqli_prepare($koneksi, "SELECT foto FROM aspirasi WHERE id_aspirasi = ?");
mysqli_stmt_bind_param($ambil, "s", $id);
mysqli_stmt_execute($ambil);
$baris = mysqli_fetch_assoc(mysqli_stmt_get_result($ambil));
mysqli_stmt_close($ambil);

$stmt = mysqli_prepare($koneksi, "DELETE FROM aspirasi WHERE id_aspirasi = ?");
mysqli_stmt_bind_param($stmt, "s", $id);

if (mysqli_stmt_execute($stmt)) {
    if ($baris) {
        hapus_foto_bukti($baris['foto']);
    }
    header("Location: aspirasi_tampil.php");
    exit;
} else {
    die("Data gagal dihapus: " . mysqli_error($koneksi));
}
