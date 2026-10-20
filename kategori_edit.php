<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

$active = 'kategori';
$error  = "";

if (!isset($_GET['id_kategori'])) {
    die("ID Kategori tidak ditemukan.");
}
$id_kategori = $_GET['id_kategori'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM kategori WHERE id_kategori = ?");
mysqli_stmt_bind_param($stmt, "s", $id_kategori);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data  = mysqli_fetch_assoc($hasil);

if (!$data) {
    die("Data kategori tidak ditemukan.");
}

if (isset($_POST['update'])) {

    $ket_kategori = trim($_POST['ket_kategori']);

    $update = mysqli_prepare(
        $koneksi,
        "UPDATE kategori SET ket_kategori = ? WHERE id_kategori = ?"
    );
    mysqli_stmt_bind_param($update, "ss", $ket_kategori, $id_kategori);

    if (mysqli_stmt_execute($update)) {
        header("Location: kategori_tampil.php");
        exit;
    } else {
        $error = "Data gagal diupdate: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Edit Kategori</h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST">

            <label>ID Kategori</label>
            <input type="text" value="<?= htmlspecialchars($data['id_kategori']) ?>" disabled>

            <label>Keterangan Kategori</label>
            <input type="text" name="ket_kategori" maxlength="100"
                   value="<?= htmlspecialchars($data['ket_kategori']) ?>" required>

            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <a href="kategori_tampil.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
