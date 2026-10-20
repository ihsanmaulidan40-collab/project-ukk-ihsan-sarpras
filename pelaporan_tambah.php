<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

$active = 'pelaporan';
$error  = "";

if (isset($_POST['simpan'])) {

    $id_pelaporan = trim($_POST['id_pelaporan']);
    $nis          = trim($_POST['nis']);
    $id_kategori  = trim($_POST['id_kategori']);
    $lokasi       = trim($_POST['lokasi']);
    $ket          = trim($_POST['ket']);
    $status       = $_POST['status'];

    $cek_id = mysqli_prepare($koneksi, "SELECT id_pelaporan FROM input_aspirasi WHERE id_pelaporan = ?");
    mysqli_stmt_bind_param($cek_id, "s", $id_pelaporan);
    mysqli_stmt_execute($cek_id);
    mysqli_stmt_store_result($cek_id);

    $cek_nis = mysqli_prepare($koneksi, "SELECT nis FROM siswa WHERE nis = ?");
    mysqli_stmt_bind_param($cek_nis, "s", $nis);
    mysqli_stmt_execute($cek_nis);
    mysqli_stmt_store_result($cek_nis);

    $cek_kategori = mysqli_prepare($koneksi, "SELECT id_kategori FROM kategori WHERE id_kategori = ?");
    mysqli_stmt_bind_param($cek_kategori, "s", $id_kategori);
    mysqli_stmt_execute($cek_kategori);
    mysqli_stmt_store_result($cek_kategori);

    if (mysqli_stmt_num_rows($cek_id) > 0) {
        $error = "ID Pelaporan sudah digunakan!";
    } elseif (mysqli_stmt_num_rows($cek_nis) === 0) {
        $error = "NIS tidak ditemukan di tabel siswa!";
    } elseif (mysqli_stmt_num_rows($cek_kategori) === 0) {
        $error = "ID Kategori tidak ditemukan di tabel kategori!";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO input_aspirasi (id_pelaporan, nis, id_kategori, lokasi, ket, status)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ssssss", $id_pelaporan, $nis, $id_kategori, $lokasi, $ket, $status);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: pelaporan_tampil.php");
            exit;
        } else {
            $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
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
    <title>Tambah Pelaporan</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Tambah Pelaporan Sarpras</h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST">

            <label>ID Pelaporan</label>
            <input type="number" name="id_pelaporan" required>

            <label>NIS</label>
            <select name="nis" required>
                <option value="">-- Pilih NIS --</option>
                <?php while ($s = mysqli_fetch_assoc($data_siswa)) { ?>
                    <option value="<?= htmlspecialchars($s['nis']) ?>"><?= htmlspecialchars($s['nis']) ?></option>
                <?php } ?>
            </select>

            <label>ID Kategori</label>
            <select name="id_kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while ($k = mysqli_fetch_assoc($data_kategori)) { ?>
                    <option value="<?= htmlspecialchars($k['id_kategori']) ?>">
                        <?= htmlspecialchars($k['id_kategori']) ?> - <?= htmlspecialchars($k['ket_kategori']) ?>
                    </option>
                <?php } ?>
            </select>

            <label>Lokasi</label>
            <input type="text" name="lokasi" maxlength="100" required>

            <label>Keterangan</label>
            <textarea name="ket" maxlength="255" required></textarea>

            <label>Status</label>
            <select name="status" required>
                <option value="Menunggu">Menunggu</option>
                <option value="Proses">Proses</option>
                <option value="Selesai">Selesai</option>
            </select>

            <div class="form-actions">
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <a href="pelaporan_tampil.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
