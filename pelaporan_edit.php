<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

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

if (isset($_POST['update'])) {

    $nis         = trim($_POST['nis']);
    $id_kategori = trim($_POST['id_kategori']);
    $lokasi      = trim($_POST['lokasi']);
    $ket         = trim($_POST['ket']);
    $status      = $_POST['status'];

    $cek_nis = mysqli_prepare($koneksi, "SELECT nis FROM siswa WHERE nis = ?");
    mysqli_stmt_bind_param($cek_nis, "s", $nis);
    mysqli_stmt_execute($cek_nis);
    mysqli_stmt_store_result($cek_nis);

    $cek_kategori = mysqli_prepare($koneksi, "SELECT id_kategori FROM kategori WHERE id_kategori = ?");
    mysqli_stmt_bind_param($cek_kategori, "s", $id_kategori);
    mysqli_stmt_execute($cek_kategori);
    mysqli_stmt_store_result($cek_kategori);

    if (mysqli_stmt_num_rows($cek_nis) === 0) {
        $error = "NIS tidak ditemukan di tabel siswa!";
    } elseif (mysqli_stmt_num_rows($cek_kategori) === 0) {
        $error = "ID Kategori tidak ditemukan di tabel kategori!";
    } else {
        $update = mysqli_prepare(
            $koneksi,
            "UPDATE input_aspirasi SET nis = ?, id_kategori = ?, lokasi = ?, ket = ?, status = ?
             WHERE id_pelaporan = ?"
        );
        mysqli_stmt_bind_param($update, "ssssss", $nis, $id_kategori, $lokasi, $ket, $status, $id_pelaporan);

        if (mysqli_stmt_execute($update)) {
            header("Location: pelaporan_tampil.php");
            exit;
        } else {
            $error = "Gagal mengupdate data: " . mysqli_error($koneksi);
        }
    }
}

$data_siswa    = mysqli_query($koneksi, "SELECT nis FROM siswa ORDER BY nis ASC");
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
        <h1>Edit Pelaporan Sarpras</h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST">

            <label>ID Pelaporan</label>
            <input type="text" value="<?= htmlspecialchars($data['id_pelaporan']) ?>" disabled>

            <label>NIS</label>
            <select name="nis" required>
                <?php while ($s = mysqli_fetch_assoc($data_siswa)) { ?>
                    <option value="<?= htmlspecialchars($s['nis']) ?>"
                        <?= $s['nis'] === $data['nis'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nis']) ?>
                    </option>
                <?php } ?>
            </select>

            <label>ID Kategori</label>
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

            <label>Status</label>
            <select name="status" required>
                <?php foreach (['Menunggu', 'Proses', 'Selesai'] as $opt) { ?>
                    <option value="<?= $opt ?>" <?= $data['status'] === $opt ? 'selected' : '' ?>>
                        <?= $opt ?>
                    </option>
                <?php } ?>
            </select>

            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <a href="pelaporan_tampil.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
