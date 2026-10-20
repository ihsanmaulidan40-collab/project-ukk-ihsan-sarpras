<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

$active = 'siswa';
$error  = "";

if (!isset($_GET['nis'])) {
    die("NIS tidak ditemukan.");
}
$nis = $_GET['nis'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM siswa WHERE nis = ?");
mysqli_stmt_bind_param($stmt, "s", $nis);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($hasil);

if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

if (isset($_POST['update'])) {

    $nama     = trim($_POST['nama']);
    $kelas    = trim($_POST['kelas']);
    $jurusan  = trim($_POST['jurusan']);
    $password = trim($_POST['password']);

    if ($password !== '') {
        $update = mysqli_prepare(
            $koneksi,
            "UPDATE siswa SET nama = ?, kelas = ?, jurusan = ?, password = ? WHERE nis = ?"
        );
        mysqli_stmt_bind_param($update, "sssss", $nama, $kelas, $jurusan, $password, $nis);
    } else {
        $update = mysqli_prepare(
            $koneksi,
            "UPDATE siswa SET nama = ?, kelas = ?, jurusan = ? WHERE nis = ?"
        );
        mysqli_stmt_bind_param($update, "ssss", $nama, $kelas, $jurusan, $nis);
    }

    if (mysqli_stmt_execute($update)) {
        header("Location: siswa_tampil.php");
        exit;
    } else {
        $error = "Data gagal diubah: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Edit Data Siswa</h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST">

            <label>NIS</label>
            <input type="text" value="<?= htmlspecialchars($siswa['nis']) ?>" disabled>

            <label>Nama</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($siswa['nama']) ?>" required>

            <label>Kelas</label>
            <input type="text" name="kelas" value="<?= htmlspecialchars($siswa['kelas']) ?>" required>

            <label>Jurusan</label>
            <input type="text" name="jurusan" value="<?= htmlspecialchars($siswa['jurusan']) ?>" required>

            <label>Password Login Siswa</label>
            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">

            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <a href="siswa_tampil.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
