<?php
require_once __DIR__ . '/includes/auth.php';
require_role('petugas');

require_once __DIR__ . '/config/koneksi.php';

$active   = 'akun';
$error    = "";
$sukses   = "";
$username = $_SESSION['username'];

if (isset($_POST['update'])) {

    $nama            = trim($_POST['nama']);
    $password_baru   = trim($_POST['password_baru']);
    $password_ulang  = trim($_POST['password_ulang']);

    if ($password_baru !== '' && $password_baru !== $password_ulang) {
        $error = "Konfirmasi password baru tidak cocok!";
    } else {
        if ($password_baru !== '') {
            $update = mysqli_prepare($koneksi, "UPDATE petugas SET nama = ?, password = ? WHERE username = ?");
            mysqli_stmt_bind_param($update, "sss", $nama, $password_baru, $username);
        } else {
            $update = mysqli_prepare($koneksi, "UPDATE petugas SET nama = ? WHERE username = ?");
            mysqli_stmt_bind_param($update, "ss", $nama, $username);
        }

        if (mysqli_stmt_execute($update)) {
            $_SESSION['nama'] = $nama;
            $sukses = "Data akun berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui akun: " . mysqli_error($koneksi);
        }
    }
}

$stmt = mysqli_prepare($koneksi, "SELECT * FROM petugas WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
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

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>
        <?php if ($sukses !== "") { ?>
            <div class="status-badge status-selesai" style="display:block; padding:10px; margin-bottom:15px;">
                <?= htmlspecialchars($sukses) ?>
            </div>
        <?php } ?>

        <form method="POST">

            <label>Username</label>
            <input type="text" value="<?= htmlspecialchars($data['username']) ?>" disabled>

            <label>Nama</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

            <label>Password Baru</label>
            <input type="password" name="password_baru" placeholder="Kosongkan jika tidak ingin mengubah password">

            <label>Ulangi Password Baru</label>
            <input type="password" name="password_ulang">

            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
            </div>

        </form>

    </div>

</div>

</body>
</html>
