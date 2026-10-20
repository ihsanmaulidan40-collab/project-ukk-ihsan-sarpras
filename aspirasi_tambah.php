<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/upload.php';

$active = 'aspirasi';
$error  = "";

if (post_melebihi_batas()) {
    $error = "Ukuran file melebihi batas server. Gunakan foto maksimal 2 MB.";
}

if (isset($_POST['simpan'])) {

    $id_aspirasi = trim($_POST['id_aspirasi']);
    $nama        = trim($_POST['nama']);
    $kelas       = trim($_POST['kelas']);
    $status      = $_POST['status'];
    $id_kategori = trim($_POST['id_kategori']);
    $feedback    = $_POST['feedback'] !== '' ? (int) $_POST['feedback'] : null;
    $nis         = trim($_POST['nis']) !== '' ? trim($_POST['nis']) : null;

    $cek_id = mysqli_prepare($koneksi, "SELECT id_aspirasi FROM aspirasi WHERE id_aspirasi = ?");
    mysqli_stmt_bind_param($cek_id, "s", $id_aspirasi);
    mysqli_stmt_execute($cek_id);
    mysqli_stmt_store_result($cek_id);

    $cek_kategori = mysqli_prepare($koneksi, "SELECT id_kategori FROM kategori WHERE id_kategori = ?");
    mysqli_stmt_bind_param($cek_kategori, "s", $id_kategori);
    mysqli_stmt_execute($cek_kategori);
    mysqli_stmt_store_result($cek_kategori);

    if (mysqli_stmt_num_rows($cek_id) > 0) {
        $error = "ID Aspirasi sudah digunakan!";
    } elseif (mysqli_stmt_num_rows($cek_kategori) === 0) {
        $error = "ID Kategori tidak ditemukan di tabel kategori!";
    } else {

        // Foto bukti (opsional): null = tidak ada foto, false = gagal upload
        $foto = simpan_foto_bukti($_FILES['foto'] ?? [], $error_foto);

        if ($foto === false) {
            $error = $error_foto;
        } else {
            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO aspirasi (id_aspirasi, nama, kelas, status, id_kategori, feedback, nis, foto)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "sssssiss", $id_aspirasi, $nama, $kelas, $status, $id_kategori, $feedback, $nis, $foto);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: aspirasi_tampil.php");
                exit;
            } else {
                hapus_foto_bukti($foto); // batal simpan -> jangan sisakan file yatim
                $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
            }
        }
    }
}

$data_siswa = mysqli_query($koneksi, "SELECT nis, nama FROM siswa ORDER BY nis ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Aspirasi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Tambah Aspirasi</h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data">

            <label>ID Aspirasi</label>
            <input type="number" name="id_aspirasi" required>

            <label>Nama</label>
            <input type="text" name="nama" required>

            <label>Kelas</label>
            <input type="text" name="kelas" required>

            <label>Status</label>
            <select name="status" required>
                <option value="Menunggu">Menunggu</option>
                <option value="Proses">Proses</option>
                <option value="Selesai">Selesai</option>
            </select>

            <label>ID Kategori</label>
            <input type="number" name="id_kategori" required>

            <label>Feedback (1-5)</label>
            <input type="number" name="feedback" min="1" max="5">

            <label>Siswa Terkait (opsional)</label>
            <select name="nis">
                <option value="">-- Tidak terkait akun siswa manapun --</option>
                <?php while ($s = mysqli_fetch_assoc($data_siswa)) { ?>
                    <option value="<?= htmlspecialchars($s['nis']) ?>">
                        <?= htmlspecialchars($s['nis']) ?> - <?= htmlspecialchars($s['nama']) ?>
                    </option>
                <?php } ?>
            </select>

            <label>Foto Bukti (opsional)</label>
            <input type="file" name="foto" accept="image/jpeg,image/png,image/webp">
            <p class="form-hint">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</p>

            <div class="form-actions">
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <a href="aspirasi_tampil.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
