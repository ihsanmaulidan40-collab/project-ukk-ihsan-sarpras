<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

$active = 'kategori';
$error  = "";

if (isset($_POST['simpan'])) {

    $id_kategori  = trim($_POST['id_kategori']);
    $ket_kategori = trim($_POST['ket_kategori']);

    $cek = mysqli_prepare($koneksi, "SELECT id_kategori FROM kategori WHERE id_kategori = ?");
    mysqli_stmt_bind_param($cek, "s", $id_kategori);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) > 0) {
        $error = "ID Kategori sudah digunakan!";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO kategori (id_kategori, ket_kategori) VALUES (?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ss", $id_kategori, $ket_kategori);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: kategori_tampil.php");
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
    <title>Tambah Kategori</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Tambah Kategori</h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST">

            <label>ID Kategori</label>
            <input type="number" name="id_kategori" required>

            <label>Keterangan Kategori</label>
            <input type="text" name="ket_kategori" maxlength="100" required>

            <div class="form-actions">
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <a href="kategori_tampil.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
