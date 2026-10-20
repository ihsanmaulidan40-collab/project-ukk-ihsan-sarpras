<?php
/**
 * Sidebar bersama, isinya menyesuaikan $_SESSION['role'].
 * Set $active sebelum include file ini, sesuai daftar per role di bawah.
 */
$active = $active ?? '';
$role   = $_SESSION['role'] ?? '';

function nav_class($key, $active)
{
    return $key === $active ? 'active' : '';
}
?>
<div class="sidebar">

    <h2>SARPRAS</h2>
    <p style="text-align:center; color:#94a3b8; font-size:12px; margin-top:-20px; margin-bottom:20px;">
        <?= htmlspecialchars(label_role($role)) ?>
        <?php if (!empty($_SESSION['nama'])) { ?>
            &middot; <?= htmlspecialchars($_SESSION['nama']) ?>
        <?php } ?>
    </p>

    <?php if ($role === 'admin') { ?>

        <a href="dashboard.php" class="<?= nav_class('dashboard', $active) ?>">Dashboard</a>
        <a href="siswa_tampil.php" class="<?= nav_class('siswa', $active) ?>">Data Siswa</a>
        <a href="kategori_tampil.php" class="<?= nav_class('kategori', $active) ?>">Data Kategori</a>
        <a href="aspirasi_tampil.php" class="<?= nav_class('aspirasi', $active) ?>">Data Aspirasi</a>
        <a href="pelaporan_tampil.php" class="<?= nav_class('pelaporan', $active) ?>">Pelaporan Sarpras</a>
        <a href="admin_tampil.php" class="<?= nav_class('admin', $active) ?>">Data Admin</a>
        <a href="petugas_tampil.php" class="<?= nav_class('petugas', $active) ?>">Data Petugas</a>

    <?php } elseif ($role === 'petugas') { ?>

        <a href="petugas_dashboard.php" class="<?= nav_class('dashboard', $active) ?>">Dashboard</a>
        <a href="petugas_aspirasi.php" class="<?= nav_class('aspirasi', $active) ?>">Kelola Aspirasi</a>
        <a href="petugas_pelaporan.php" class="<?= nav_class('pelaporan', $active) ?>">Kelola Pelaporan</a>
        <a href="petugas_kategori.php" class="<?= nav_class('kategori', $active) ?>">Data Kategori</a>
        <a href="petugas_siswa.php" class="<?= nav_class('siswa', $active) ?>">Data Siswa</a>
        <a href="petugas_akun.php" class="<?= nav_class('akun', $active) ?>">Akun Saya</a>

    <?php } elseif ($role === 'siswa') { ?>

        <a href="siswa_dashboard.php" class="<?= nav_class('dashboard', $active) ?>">Dashboard</a>
        <a href="siswa_aspirasi.php" class="<?= nav_class('aspirasi', $active) ?>">Aspirasi Saya</a>
        <a href="siswa_pelaporan.php" class="<?= nav_class('pelaporan', $active) ?>">Pelaporan Saya</a>
        <a href="siswa_akun.php" class="<?= nav_class('akun', $active) ?>">Akun Saya</a>

    <?php } ?>

    <a href="logout.php" class="logout">Logout</a>

</div>
