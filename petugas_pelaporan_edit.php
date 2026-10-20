<?php
require_once __DIR__ . '/includes/auth.php';
require_role('petugas');

require_once __DIR__ . '/config/koneksi.php';

$active = 'pelaporan';
$error  = "";

if (!isset($_GET['id_pelaporan'])) {
    die("ID pelaporan tidak ditemukan.");
}
$id_pelaporan = $_GET['id_pelaporan'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM input_aspirasi WHERE id_pelaporan = ?");
mysqli_stmt_bind_param($stmt, "s", $id_pelaporan);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data  = mysqli_fetch_assoc($hasil);

if (!$data) {
    die("Data pelaporan tidak ditemukan.");
}

// Petugas hanya boleh mengubah status penanganan, bukan data laporan aslinya.
if (isset($_POST['update'])) {

    $status = $_POST['status'];

    $update = mysqli_prepare(
        $koneksi,
        "UPDATE input_aspirasi SET status = ? WHERE id_pelaporan = ?"
    );
    mysqli_stmt_bind_param($update, "ss", $status, $id_pelaporan);

    if (mysqli_stmt_execute($update)) {
        header("Location: petugas_pelaporan.php");
        exit;
    } else {
        $error = "Gagal mengubah status: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Pelaporan</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Proses Pelaporan #<?= htmlspecialchars($data['id_pelaporan']) ?></h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <p><b>NIS Pelapor:</b> <?= htmlspecialchars($data['nis']) ?></p>
        <p><b>ID Kategori:</b> <?= htmlspecialchars($data['id_kategori']) ?></p>
        <p><b>Lokasi:</b> <?= htmlspecialchars($data['lokasi']) ?></p>
        <p><b>Keterangan:</b> <?= htmlspecialchars($data['ket']) ?></p>

        <form method="POST">

            <label>Status Penanganan</label>
            <select name="status" required>
                <?php foreach (['Menunggu', 'Proses', 'Selesai'] as $opt) { ?>
                    <option value="<?= $opt ?>" <?= $data['status'] === $opt ? 'selected' : '' ?>>
                        <?= $opt ?>
                    </option>
                <?php } ?>
            </select>

            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Simpan</button>
                <a href="petugas_pelaporan.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
