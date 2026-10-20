<?php
require_once __DIR__ . '/includes/auth.php';
require_role('siswa');

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/upload.php';

$active = 'aspirasi';
$error  = "";
$nis    = $_SESSION['nis'];
$nama   = $_SESSION['nama'];
$kelas  = $_SESSION['kelas'];

if (post_melebihi_batas()) {
    $error = "Ukuran file melebihi batas server. Gunakan foto maksimal 2 MB.";
}

if (isset($_POST['simpan'])) {

    $id_kategori = trim($_POST['id_kategori']);

    $cek_kategori = mysqli_prepare($koneksi, "SELECT id_kategori FROM kategori WHERE id_kategori = ?");
    mysqli_stmt_bind_param($cek_kategori, "s", $id_kategori);
    mysqli_stmt_execute($cek_kategori);
    mysqli_stmt_store_result($cek_kategori);

    if (mysqli_stmt_num_rows($cek_kategori) === 0) {
        $error = "ID Kategori tidak ditemukan!";
    } else {

        // Foto bukti (opsional): null = tidak ada foto, false = gagal upload
        $foto = simpan_foto_bukti($_FILES['foto'] ?? [], $error_foto);

        if ($foto === false) {
            $error = $error_foto;
        } else {

            // ID Aspirasi dibuat otomatis (MAX + 1) supaya siswa tidak perlu mengisi ID manual.
            $hasil_max   = mysqli_query($koneksi, "SELECT COALESCE(MAX(id_aspirasi), 0) + 1 AS id_baru FROM aspirasi");
            $id_aspirasi = (int) mysqli_fetch_assoc($hasil_max)['id_baru'];

            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO aspirasi (id_aspirasi, nama, kelas, status, id_kategori, feedback, nis, foto)
                 VALUES (?, ?, ?, 'Menunggu', ?, NULL, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "isssss", $id_aspirasi, $nama, $kelas, $id_kategori, $nis, $foto);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: siswa_aspirasi.php");
                exit;
            } else {
                hapus_foto_bukti($foto); // batal simpan -> jangan sisakan file yatim
                $error = "Gagal mengirim aspirasi: " . mysqli_error($koneksi);
            }
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
    <title>Kirim Aspirasi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Kirim Aspirasi</h1>
        <p>Sampaikan masukan/aspirasi Anda terkait sarana &amp; prasarana sekolah</p>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data">

            <label>Nama</label>
            <input type="text" value="<?= htmlspecialchars($nama) ?>" disabled>

            <label>Kelas</label>
            <input type="text" value="<?= htmlspecialchars($kelas) ?>" disabled>

            <label>Kategori</label>
            <select name="id_kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while ($k = mysqli_fetch_assoc($data_kategori)) { ?>
                    <option value="<?= htmlspecialchars($k['id_kategori']) ?>">
                        <?= htmlspecialchars($k['id_kategori']) ?> - <?= htmlspecialchars($k['ket_kategori']) ?>
                    </option>
                <?php } ?>
            </select>

            <label>Foto Bukti (opsional)</label>
            <input type="file" name="foto" accept="image/jpeg,image/png,image/webp">
            <p class="form-hint">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</p>

            <div class="form-actions">
                <button type="submit" name="simpan" class="btn btn-primary">Kirim</button>
                <a href="siswa_aspirasi.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
