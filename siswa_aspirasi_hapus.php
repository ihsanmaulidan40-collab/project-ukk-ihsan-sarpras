<?php
require_once __DIR__ . '/includes/auth.php';
require_role('siswa');

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/upload.php';

$nis = $_SESSION['nis'];

if (!isset($_GET['id_aspirasi'])) {
    die("ID Aspirasi tidak ditemukan.");
}
$id_aspirasi = $_GET['id_aspirasi'];

$stmt = mysqli_prepare($koneksi, "SELECT status, foto FROM aspirasi WHERE id_aspirasi = ? AND nis = ?");
mysqli_stmt_bind_param($stmt, "ss", $id_aspirasi, $nis);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    die("Data aspirasi tidak ditemukan, atau bukan milik Anda.");
}

if ($data['status'] !== 'Menunggu') {
    die("Aspirasi yang sudah diproses petugas tidak bisa dihapus. "
        . "<a href='siswa_aspirasi.php'>Kembali</a>");
}

$hapus = mysqli_prepare($koneksi, "DELETE FROM aspirasi WHERE id_aspirasi = ? AND nis = ?");
mysqli_stmt_bind_param($hapus, "ss", $id_aspirasi, $nis);

if (mysqli_stmt_execute($hapus)) {
    hapus_foto_bukti($data['foto']);
    header("Location: siswa_aspirasi.php");
    exit;
} else {
    die("Gagal menghapus data: " . mysqli_error($koneksi));
}
