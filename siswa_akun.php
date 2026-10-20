<?php
require_once __DIR__ . '/includes/auth.php';
require_role('siswa');

require_once __DIR__ . '/config/koneksi.php';

$active = 'akun';
$error  = "";
$sukses = "";
$nis    = $_SESSION['nis'];

if (isset($_POST['update'])) {

    $password_lama   = trim($_POST['password_lama']);
    $password_baru   = trim($_POST['password_baru']);
    $password_ulang  = trim($_POST['password_ulang']);

    $stmt = mysqli_prepare($koneksi, "SELECT password FROM siswa WHERE nis = ?");
    mysqli_stmt_bind_param($stmt, "s", $nis);
    mysqli_stmt_execute($stmt);
    $cek = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$cek || !password_cocok($password_lama, $cek['password'])) {
        $error = "Password lama salah!";
    } elseif ($password_baru === '' || $password_baru !== $password_ulang) {
        $error = "Password baru dan konfirmasinya harus sama & tidak boleh kosong!";
    } else {
        $update = mysqli_prepare($koneksi, "UPDATE siswa SET password = ? WHERE nis = ?");
        $password_simpan = password_hash($password_baru, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($update, "ss", $password_simpan, $nis);

        if (mysqli_stmt_execute($update)) {
            $sukses = "Password berhasil diubah.";
        } else {
            $error = "Gagal mengubah password: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Akun Saya</h1>
    </div>

    <div class="panel">

        <p><b>NIS:</b> <?= htmlspecialchars($_SESSION['nis']) ?></p>
        <p><b>Nama:</b> <?= htmlspecialchars($_SESSION['nama']) ?></p>
        <p><b>Kelas:</b> <?= htmlspecialchars($_SESSION['kelas']) ?></p>
        <p style="font-size:13px; color:#64748b; margin-top:6px;">
            Nama, kelas, dan jurusan hanya bisa diubah oleh Admin lewat menu Data Siswa.
        </p>

        <hr style="margin:20px 0; border:none; border-top:1px solid #e2e8f0;">

        <h2 style="margin-bottom:16px; font-size:17px;">Ganti Password</h2>

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>
        <?php if ($sukses !== "") { ?>
            <div class="status-badge status-selesai" style="display:block; padding:10px; margin-bottom:15px;">
                <?= htmlspecialchars($sukses) ?>
            </div>
        <?php } ?>

        <form method="POST">

            <label>Password Lama</label>
            <input type="password" name="password_lama" required>

            <label>Password Baru</label>
            <input type="password" name="password_baru" required>

            <label>Ulangi Password Baru</label>
            <input type="password" name="password_ulang" required>

            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Simpan Password</button>
            </div>

        </form>

    </div>

</div>

</body>
</html>
