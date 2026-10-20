<?php
require_once __DIR__ . '/includes/auth.php';
require_role('siswa');

require_once __DIR__ . '/config/koneksi.php';

$active = 'pelaporan';
$error  = "";
$nis    = $_SESSION['nis'];

if (isset($_POST['simpan'])) {

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

        // ID Pelaporan dibuat otomatis (MAX + 1) supaya siswa tidak perlu mengisi ID manual.
        $hasil_max    = mysqli_query($koneksi, "SELECT COALESCE(MAX(id_pelaporan), 0) + 1 AS id_baru FROM input_aspirasi");
        $id_pelaporan = (int) mysqli_fetch_assoc($hasil_max)['id_baru'];

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO input_aspirasi (id_pelaporan, nis, id_kategori, lokasi, ket, status)
             VALUES (?, ?, ?, ?, ?, 'Menunggu')"
        );
        mysqli_stmt_bind_param($stmt, "sssss", $id_pelaporan, $nis, $id_kategori, $lokasi, $ket);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: siswa_pelaporan.php");
            exit;
        } else {
            $error = "Gagal mengirim pelaporan: " . mysqli_error($koneksi);
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
    <title>Buat Pelaporan</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Buat Pelaporan Sarpras</h1>
        <p>Laporkan kerusakan atau kebutuhan sarana &amp; prasarana</p>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST">

            <label>Kategori</label>
            <select name="id_kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while ($k = mysqli_fetch_assoc($data_kategori)) { ?>
                    <option value="<?= htmlspecialchars($k['id_kategori']) ?>">
                        <?= htmlspecialchars($k['id_kategori']) ?> - <?= htmlspecialchars($k['ket_kategori']) ?>
                    </option>
                <?php } ?>
            </select>

            <label>Lokasi</label>
            <input type="text" name="lokasi" maxlength="100" required placeholder="Contoh: Ruang Kelas 12 RPL 1">

            <label>Keterangan</label>
            <textarea name="ket" maxlength="255" required placeholder="Jelaskan kondisi kerusakan/kebutuhan"></textarea>

            <div class="form-actions">
                <button type="submit" name="simpan" class="btn btn-primary">Kirim</button>
                <a href="siswa_pelaporan.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
