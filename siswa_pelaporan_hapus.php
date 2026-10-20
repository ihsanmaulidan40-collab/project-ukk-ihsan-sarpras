<?php
require_once __DIR__ . '/includes/auth.php';
require_role('siswa');

require_once __DIR__ . '/config/koneksi.php';

$nis = $_SESSION['nis'];

if (!isset($_GET['id_pelaporan'])) {
    die("ID pelaporan tidak ditemukan.");
}
$id_pelaporan = $_GET['id_pelaporan'];

$stmt = mysqli_prepare($koneksi, "SELECT status FROM input_aspirasi WHERE id_pelaporan = ? AND nis = ?");
mysqli_stmt_bind_param($stmt, "ss", $id_pelaporan, $nis);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    die("Data pelaporan tidak ditemukan, atau bukan milik Anda.");
}

if ($data['status'] !== 'Menunggu') {
    die("Pelaporan yang sudah diproses petugas tidak bisa dihapus. "
        . "<a href='siswa_pelaporan.php'>Kembali</a>");
}

$hapus = mysqli_prepare($koneksi, "DELETE FROM input_aspirasi WHERE id_pelaporan = ? AND nis = ?");
mysqli_stmt_bind_param($hapus, "ss", $id_pelaporan, $nis);

if (mysqli_stmt_execute($hapus)) {
    header("Location: siswa_pelaporan.php");
    exit;
} else {
    die("Gagal menghapus data: " . mysqli_error($koneksi));
}
