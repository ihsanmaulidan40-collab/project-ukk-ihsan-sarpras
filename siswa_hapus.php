<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

if (!isset($_GET['nis'])) {
    die("NIS tidak ditemukan.");
}
$nis = $_GET['nis'];

mysqli_begin_transaction($koneksi);

try {

    // Hapus pelaporan (input_aspirasi) milik siswa ini terlebih dahulu (FK)
    $stmt1 = mysqli_prepare($koneksi, "DELETE FROM input_aspirasi WHERE nis = ?");
    mysqli_stmt_bind_param($stmt1, "s", $nis);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    // Baru hapus data siswa
    $stmt2 = mysqli_prepare($koneksi, "DELETE FROM siswa WHERE nis = ?");
    mysqli_stmt_bind_param($stmt2, "s", $nis);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    mysqli_commit($koneksi);

    header("Location: siswa_tampil.php");
    exit;

} catch (mysqli_sql_exception $e) {
    mysqli_rollback($koneksi);
    die("Data gagal dihapus: " . $e->getMessage());
}
