<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/koneksi.php';

// Kalau sudah login, tidak perlu daftar lagi
if (!empty($_SESSION['login'])) {
    header("Location: " . dashboard_url_for_role($_SESSION['role'] ?? ''));
    exit;
}

$error   = "";
$nis     = "";
$nama    = "";
$kelas   = "";
$jurusan = "";

if (isset($_POST['daftar'])) {

    $nis           = trim($_POST['nis']);
    $nama          = trim($_POST['nama']);
    $kelas         = trim($_POST['kelas']);
    $jurusan       = trim($_POST['jurusan']);
    $password      = trim($_POST['password']);
    $password_ulang = trim($_POST['password_ulang']);

    if ($nis === '' || $nama === '' || $kelas === '' || $jurusan === '' || $password === '') {
        $error = "Semua kolom wajib diisi!";
    } elseif (!preg_match('/^[A-Za-z0-9]{3,20}$/', $nis)) {
        $error = "NIS hanya boleh huruf/angka, 3-20 karakter (tanpa spasi).";
    } elseif (mb_strlen($nama) > 100 || mb_strlen($kelas) > 20 || mb_strlen($jurusan) > 50) {
        $error = "Nama maks. 100, kelas maks. 20, dan jurusan maks. 50 karakter.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password !== $password_ulang) {
        $error = "Password dan konfirmasinya tidak sama!";
    } else {

        $cek = mysqli_prepare($koneksi, "SELECT nis FROM siswa WHERE nis = ?");
        mysqli_stmt_bind_param($cek, "s", $nis);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);
        $nis_terpakai = mysqli_stmt_num_rows($cek) > 0;
        mysqli_stmt_close($cek);

        if ($nis_terpakai) {
            $error = "NIS sudah terdaftar. Jika lupa password, minta Admin mereset lewat menu Data Siswa.";
        } else {

            // Akun hasil pendaftaran disimpan dengan password ter-hash.
            // login.php mengenali hash maupun password teks biasa (akun lama).
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO siswa (nis, nama, kelas, jurusan, password) VALUES (?, ?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "sssss", $nis, $nama, $kelas, $jurusan, $password_hash);

            try {
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: login.php?daftar=sukses");
                    exit;
                }
                $error = "Pendaftaran gagal. Silakan coba lagi.";
            } catch (mysqli_sql_exception $e) {
                // Kemungkinan NIS dobel karena dua orang daftar bersamaan
                $error = "Pendaftaran gagal. NIS mungkin sudah terdaftar.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa - Pengadaan Sarpras</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-box login-box-lebar">

        <h2>DAFTAR AKUN SISWA</h2>

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST">

            <label>NIS</label>
            <input type="text" name="nis" value="<?= htmlspecialchars($nis) ?>" maxlength="20" required autofocus>
            <p class="form-hint">NIS dipakai sebagai username saat login.</p>

            <label>Nama Lengkap</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" maxlength="100" required>

            <label>Kelas</label>
            <input type="text" name="kelas" value="<?= htmlspecialchars($kelas) ?>" maxlength="20" placeholder="Contoh: XII RPL 1" required>

            <label>Jurusan</label>
            <input type="text" name="jurusan" value="<?= htmlspecialchars($jurusan) ?>" maxlength="50" placeholder="Contoh: RPL" required>

            <label>Password</label>
            <input type="password" name="password" minlength="6" required>
            <p class="form-hint">Minimal 6 karakter.</p>

            <label>Ulangi Password</label>
            <input type="password" name="password_ulang" minlength="6" required>

            <button type="submit" name="daftar" class="btn btn-primary" style="width:100%; margin-top:20px;">
                Daftar
            </button>

        </form>

        <p class="auth-link">
            Sudah punya akun? <a href="login.php?role=siswa">Login di sini</a>
        </p>

    </div>
</div>

</body>
</html>
