<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

$active = 'siswa';
$error  = "";

if (isset($_POST['simpan'])) {

    $nis      = trim($_POST['nis']);
    $nama     = trim($_POST['nama']);
    $kelas    = trim($_POST['kelas']);
    $jurusan  = trim($_POST['jurusan']);
    $password = trim($_POST['password']);

    $cek = mysqli_prepare($koneksi, "SELECT nis FROM siswa WHERE nis = ?");
    mysqli_stmt_bind_param($cek, "s", $nis);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) > 0) {
        $error = "NIS sudah digunakan!";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO siswa (nis, nama, kelas, jurusan, password) VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "sssss", $nis, $nama, $kelas, $jurusan, $password);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: siswa_tampil.php");
            exit;
        } else {
            $error = "Data gagal ditambahkan: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Tambah Data Siswa</h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST">

            <label>NIS</label>
            <input type="text" name="nis" required>

            <label>Nama</label>
            <input type="text" name="nama" required>

            <label>Kelas</label>
            <input type="text" name="kelas" required>

            <label>Jurusan</label>
            <input type="text" name="jurusan" required>

            <label>Password Login Siswa</label>
            <input type="password" name="password" required>
            <p style="font-size:12px; color:#64748b; margin-top:4px;">
                Siswa login pakai NIS di atas sebagai username, dan password ini.
            </p>

            <div class="form-actions">
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <a href="siswa_tampil.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
