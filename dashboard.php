<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

$active = 'dashboard';

function hitung_total($koneksi, $tabel)
{
    $hasil = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM `$tabel`");
    return $hasil ? (int) mysqli_fetch_assoc($hasil)['total'] : 0;
}

function hitung_status($koneksi, $tabel, $status)
{
    $stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM `$tabel` WHERE status = ?");
    mysqli_stmt_bind_param($stmt, "s", $status);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    return (int) mysqli_fetch_assoc($hasil)['total'];
}

$total_siswa     = hitung_total($koneksi, 'siswa');
$total_kategori  = hitung_total($koneksi, 'kategori');
$total_aspirasi  = hitung_total($koneksi, 'aspirasi');
$total_pelaporan = hitung_total($koneksi, 'input_aspirasi');
$total_petugas   = hitung_total($koneksi, 'petugas');

// Status gabungan Aspirasi + Pelaporan, supaya dashboard mencerminkan semua yang perlu ditindaklanjuti
$total_menunggu = hitung_status($koneksi, 'aspirasi', 'Menunggu') + hitung_status($koneksi, 'input_aspirasi', 'Menunggu');
$total_proses   = hitung_status($koneksi, 'aspirasi', 'Proses')   + hitung_status($koneksi, 'input_aspirasi', 'Proses');
$total_selesai  = hitung_status($koneksi, 'aspirasi', 'Selesai')  + hitung_status($koneksi, 'input_aspirasi', 'Selesai');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pengadaan Sarpras</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Dashboard Admin</h1>
        <p>Selamat datang, <b><?= htmlspecialchars($_SESSION['username']) ?></b></p>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Total Siswa</h3>
            <div class="number"><?= $total_siswa ?></div>
        </div>
        <div class="card">
            <h3>Total Kategori</h3>
            <div class="number"><?= $total_kategori ?></div>
        </div>
        <div class="card">
            <h3>Total Aspirasi</h3>
            <div class="number"><?= $total_aspirasi ?></div>
        </div>
        <div class="card">
            <h3>Total Pelaporan</h3>
            <div class="number"><?= $total_pelaporan ?></div>
        </div>
        <div class="card">
            <h3>Total Petugas</h3>
            <div class="number"><?= $total_petugas ?></div>
        </div>
    </div>

    <h2 style="margin-bottom: 18px;">Status Aspirasi + Pelaporan</h2>

    <div class="status-container">
        <div class="status-card">
            <h3>Menunggu</h3>
            <div class="status-number"><?= $total_menunggu ?></div>
        </div>
        <div class="status-card">
            <h3>Proses</h3>
            <div class="status-number"><?= $total_proses ?></div>
        </div>
        <div class="status-card">
            <h3>Selesai</h3>
            <div class="status-number"><?= $total_selesai ?></div>
        </div>
    </div>

</div>

</body>
</html>
