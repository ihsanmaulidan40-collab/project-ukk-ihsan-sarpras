<?php
require_once __DIR__ . '/includes/auth.php';
require_role('petugas');

require_once __DIR__ . '/config/koneksi.php';

$active = 'dashboard';

function hitung_status_tabel($koneksi, $tabel, $status)
{
    $stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM `$tabel` WHERE status = ?");
    mysqli_stmt_bind_param($stmt, "s", $status);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    return (int) mysqli_fetch_assoc($hasil)['total'];
}

$aspirasi_menunggu = hitung_status_tabel($koneksi, 'aspirasi', 'Menunggu');
$aspirasi_proses   = hitung_status_tabel($koneksi, 'aspirasi', 'Proses');
$aspirasi_selesai  = hitung_status_tabel($koneksi, 'aspirasi', 'Selesai');

$pelaporan_menunggu = hitung_status_tabel($koneksi, 'input_aspirasi', 'Menunggu');
$pelaporan_proses   = hitung_status_tabel($koneksi, 'input_aspirasi', 'Proses');
$pelaporan_selesai  = hitung_status_tabel($koneksi, 'input_aspirasi', 'Selesai');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Dashboard Petugas</h1>
        <p>Selamat datang, <b><?= htmlspecialchars($_SESSION['nama']) ?></b></p>
    </div>

    <h2 style="margin-bottom: 18px;">Aspirasi</h2>
    <div class="status-container">
        <div class="status-card">
            <h3>Menunggu</h3>
            <div class="status-number"><?= $aspirasi_menunggu ?></div>
        </div>
        <div class="status-card">
            <h3>Proses</h3>
            <div class="status-number"><?= $aspirasi_proses ?></div>
        </div>
        <div class="status-card">
            <h3>Selesai</h3>
            <div class="status-number"><?= $aspirasi_selesai ?></div>
        </div>
    </div>

    <h2 style="margin: 25px 0 18px;">Pelaporan Sarpras</h2>
    <div class="status-container">
        <div class="status-card">
            <h3>Menunggu</h3>
            <div class="status-number"><?= $pelaporan_menunggu ?></div>
        </div>
        <div class="status-card">
            <h3>Proses</h3>
            <div class="status-number"><?= $pelaporan_proses ?></div>
        </div>
        <div class="status-card">
            <h3>Selesai</h3>
            <div class="status-number"><?= $pelaporan_selesai ?></div>
        </div>
    </div>

    <div class="panel" style="margin-top:25px;">
        <div class="toolbar" style="display:flex; gap:10px; margin-bottom:0;">
            <a href="petugas_aspirasi.php" class="btn btn-primary">Kelola Aspirasi</a>
            <a href="petugas_pelaporan.php" class="btn btn-primary">Kelola Pelaporan</a>
        </div>
    </div>

</div>

</body>
</html>
