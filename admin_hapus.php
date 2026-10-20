<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

if (!isset($_GET['username'])) {
    die("Username tidak ditemukan.");
}
$username = $_GET['username'];

if ($username === $_SESSION['username']) {
    die("Tidak bisa menghapus akun yang sedang login. "
        . "<a href='admin_tampil.php'>Kembali</a>");
}

$total = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM admin"))['total'];

if ((int) $total <= 1) {
    die("Tidak bisa menghapus admin terakhir. <a href='admin_tampil.php'>Kembali</a>");
}

$hapus = mysqli_prepare($koneksi, "DELETE FROM admin WHERE username = ?");
mysqli_stmt_bind_param($hapus, "s", $username);

if (mysqli_stmt_execute($hapus)) {
    header("Location: admin_tampil.php");
    exit;
} else {
    die("Gagal menghapus data: " . mysqli_error($koneksi));
}
