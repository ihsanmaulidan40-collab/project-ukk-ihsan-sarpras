<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

$active = 'petugas';
$error  = "";

if (!isset($_GET['username'])) {
    die("Username tidak ditemukan.");
}
$username_lama = $_GET['username'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM petugas WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username_lama);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data  = mysqli_fetch_assoc($hasil);

if (!$data) {
    die("Data petugas tidak ditemukan.");
}

if (isset($_POST['update'])) {

    $username = trim($_POST['username']);
    $nama     = trim($_POST['nama']);
    $password = trim($_POST['password']);

    if ($username !== $username_lama) {
        $cek = mysqli_prepare($koneksi, "SELECT username FROM petugas WHERE username = ?");
        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $error = "Username sudah digunakan!";
        }
    }

    if ($error === "") {
        $update = mysqli_prepare(
            $koneksi,
            "UPDATE petugas SET username = ?, nama = ?, password = ? WHERE username = ?"
        );
        mysqli_stmt_bind_param($update, "ssss", $username, $nama, $password, $username_lama);

        if (mysqli_stmt_execute($update)) {
            header("Location: petugas_tampil.php");
            exit;
        } else {
            $error = "Gagal mengubah data: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Petugas</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Edit Petugas</h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST">

            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>

            <label>Nama</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

            <label>Password</label>
            <input type="password" name="password" value="<?= htmlspecialchars($data['password']) ?>" required>

            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <a href="petugas_tampil.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
