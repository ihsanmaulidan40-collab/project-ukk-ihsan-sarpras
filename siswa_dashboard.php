<?php
require_once __DIR__ . '/includes/auth.php';
require_role('siswa');

require_once __DIR__ . '/config/koneksi.php';

$active = 'dashboard';
$nis    = $_SESSION['nis'];

function hitung_milik($koneksi, $tabel, $nis)
{
    $stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM `$tabel` WHERE nis = ?");
    mysqli_stmt_bind_param($stmt, "s", $nis);
    mysqli_stmt_execute($stmt);
    return (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
}

$total_aspirasi  = hitung_milik($koneksi, 'aspirasi', $nis);
$total_pelaporan = hitung_milik($koneksi, 'input_aspirasi', $nis);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Dashboard Siswa</h1>
        <p>Selamat datang, <b><?= htmlspecialchars($_SESSION['nama']) ?></b> (<?= htmlspecialchars($_SESSION['kelas']) ?>)</p>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Aspirasi Saya</h3>
            <div class="number"><?= $total_aspirasi ?></div>
        </div>
        <div class="card">
            <h3>Pelaporan Saya</h3>
            <div class="number"><?= $total_pelaporan ?></div>
        </div>
    </div>

    <div class="panel">
        <div class="toolbar" style="display:flex; gap:10px; margin-bottom:0;">
            <a href="siswa_aspirasi_tambah.php" class="btn btn-primary">+ Kirim Aspirasi</a>
            <a href="siswa_pelaporan_tambah.php" class="btn btn-primary">+ Buat Pelaporan</a>
        </div>
    </div>

</div>

</body>
</html>
