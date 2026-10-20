<?php
require_once __DIR__ . '/includes/auth.php';
require_role('siswa');

require_once __DIR__ . '/config/koneksi.php';

$active = 'pelaporan';
$error  = "";
$nis    = $_SESSION['nis'];

if (!isset($_GET['id_pelaporan'])) {
    die("ID pelaporan tidak ditemukan.");
}
$id_pelaporan = $_GET['id_pelaporan'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM input_aspirasi WHERE id_pelaporan = ? AND nis = ?");
mysqli_stmt_bind_param($stmt, "ss", $id_pelaporan, $nis);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    die("Data pelaporan tidak ditemukan, atau bukan milik Anda.");
}

if ($data['status'] !== 'Menunggu') {
    die("Pelaporan yang sudah diproses petugas tidak bisa diubah lagi. "
        . "<a href='siswa_pelaporan.php'>Kembali</a>");
}

if (isset($_POST['update'])) {

    $id_kategori = trim($_POST['id_kategori']);
    $lokasi      = trim($_POST['lokasi']);
    $ket         = trim($_POST['ket']);

    $cek_kategori = mysqli_prepare($koneksi, "SELECT id_kategori FROM kategori WHERE id_kategori = ?");
    mysqli_stmt_bind_param($cek_kategori, "s", $id_kategori);
    mysqli_stmt_execute($cek_kategori);
    mysqli_stmt_store_result($cek_kategori);

    if (mysqli_stmt_num_rows($cek_kategori) === 0) {
        $error = "ID Kategori tidak ditemukan!";
    } else {
        $update = mysqli_prepare(
            $koneksi,
            "UPDATE input_aspirasi SET id_kategori = ?, lokasi = ?, ket = ? WHERE id_pelaporan = ? AND nis = ?"
        );
        mysqli_stmt_bind_param($update, "sssss", $id_kategori, $lokasi, $ket, $id_pelaporan, $nis);

        if (mysqli_stmt_execute($update)) {
            header("Location: siswa_pelaporan.php");
            exit;
        } else {
            $error = "Gagal mengubah data: " . mysqli_error($koneksi);
        }
    }
}

$data_kategori = mysqli_query($koneksi, "SELECT id_kategori, ket_kategori FROM kategori ORDER BY id_kategori ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelaporan</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Edit Pelaporan #<?= htmlspecialchars($data['id_pelaporan']) ?></h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST">

            <label>Kategori</label>
            <select name="id_kategori" required>
                <?php while ($k = mysqli_fetch_assoc($data_kategori)) { ?>
                    <option value="<?= htmlspecialchars($k['id_kategori']) ?>"
                        <?= (string) $k['id_kategori'] === (string) $data['id_kategori'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['id_kategori']) ?> - <?= htmlspecialchars($k['ket_kategori']) ?>
                    </option>
                <?php } ?>
            </select>

            <label>Lokasi</label>
            <input type="text" name="lokasi" maxlength="100" value="<?= htmlspecialchars($data['lokasi']) ?>" required>

            <label>Keterangan</label>
            <textarea name="ket" maxlength="255" required><?= htmlspecialchars($data['ket']) ?></textarea>

            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Simpan</button>
                <a href="siswa_pelaporan.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
