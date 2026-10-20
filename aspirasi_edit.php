<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/upload.php';

$active = 'aspirasi';
$error  = "";

if (!isset($_GET['id_aspirasi'])) {
    die("ID Aspirasi tidak ditemukan.");
}
$id_aspirasi = $_GET['id_aspirasi'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM aspirasi WHERE id_aspirasi = ?");
mysqli_stmt_bind_param($stmt, "s", $id_aspirasi);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data  = mysqli_fetch_assoc($hasil);

if (!$data) {
    die("Data aspirasi tidak ditemukan.");
}

$data_siswa = mysqli_query($koneksi, "SELECT nis, nama FROM siswa ORDER BY nis ASC");

if (post_melebihi_batas()) {
    $error = "Ukuran file melebihi batas server. Gunakan foto maksimal 2 MB.";
}

if (isset($_POST['update'])) {

    $nama        = trim($_POST['nama']);
    $kelas       = trim($_POST['kelas']);
    $status      = $_POST['status'];
    $id_kategori = trim($_POST['id_kategori']);
    $feedback    = $_POST['feedback'] !== '' ? (int) $_POST['feedback'] : null;
    $nis         = trim($_POST['nis']) !== '' ? trim($_POST['nis']) : null;

    $cek_kategori = mysqli_prepare($koneksi, "SELECT id_kategori FROM kategori WHERE id_kategori = ?");
    mysqli_stmt_bind_param($cek_kategori, "s", $id_kategori);
    mysqli_stmt_execute($cek_kategori);
    mysqli_stmt_store_result($cek_kategori);

    if (mysqli_stmt_num_rows($cek_kategori) === 0) {
        $error = "ID Kategori tidak ditemukan di tabel kategori!";
    } else {

        $foto_baru = simpan_foto_bukti($_FILES['foto'] ?? [], $error_foto);

        if ($foto_baru === false) {
            $error = $error_foto;
        } else {

            // Tentukan foto akhir: ganti dengan yang baru, hapus, atau biarkan
            $foto_lama  = $data['foto'];
            $foto_akhir = $foto_lama;
            if ($foto_baru !== null) {
                $foto_akhir = $foto_baru;
            } elseif (!empty($_POST['hapus_foto'])) {
                $foto_akhir = null;
            }

            $update = mysqli_prepare(
                $koneksi,
                "UPDATE aspirasi SET nama = ?, kelas = ?, status = ?, id_kategori = ?, feedback = ?, nis = ?, foto = ?
                 WHERE id_aspirasi = ?"
            );
            mysqli_stmt_bind_param($update, "ssssisss", $nama, $kelas, $status, $id_kategori, $feedback, $nis, $foto_akhir, $id_aspirasi);

            if (mysqli_stmt_execute($update)) {
                if ($foto_akhir !== $foto_lama) {
                    hapus_foto_bukti($foto_lama); // buang file lama yang sudah tidak dipakai
                }
                header("Location: aspirasi_tampil.php");
                exit;
            } else {
                hapus_foto_bukti($foto_baru); // batal simpan -> jangan sisakan file yatim
                $error = "Gagal mengubah data: " . mysqli_error($koneksi);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Aspirasi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Edit Aspirasi</h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data">

            <label>ID Aspirasi</label>
            <input type="text" value="<?= htmlspecialchars($data['id_aspirasi']) ?>" disabled>

            <label>Nama</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

            <label>Kelas</label>
            <input type="text" name="kelas" value="<?= htmlspecialchars($data['kelas']) ?>" required>

            <label>Status</label>
            <select name="status" required>
                <?php foreach (['Menunggu', 'Proses', 'Selesai'] as $opt) { ?>
                    <option value="<?= $opt ?>" <?= $data['status'] === $opt ? 'selected' : '' ?>>
                        <?= $opt ?>
                    </option>
                <?php } ?>
            </select>

            <label>ID Kategori</label>
            <input type="number" name="id_kategori" value="<?= htmlspecialchars($data['id_kategori']) ?>" required>

            <label>Feedback (1-5)</label>
            <input type="number" name="feedback" min="1" max="5" value="<?= htmlspecialchars($data['feedback'] ?? '') ?>">

            <label>Siswa Terkait (opsional)</label>
            <select name="nis">
                <option value="">-- Tidak terkait akun siswa manapun --</option>
                <?php while ($s = mysqli_fetch_assoc($data_siswa)) { ?>
                    <option value="<?= htmlspecialchars($s['nis']) ?>"
                        <?= $s['nis'] === $data['nis'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nis']) ?> - <?= htmlspecialchars($s['nama']) ?>
                    </option>
                <?php } ?>
            </select>

            <label>Foto Bukti</label>
            <?php if (!empty($data['foto'])) { ?>
                <a href="<?= htmlspecialchars(url_foto_bukti($data['foto'])) ?>" target="_blank" rel="noopener">
                    <img class="foto-preview" src="<?= htmlspecialchars(url_foto_bukti($data['foto'])) ?>" alt="Foto bukti saat ini">
                </a>
                <label class="checkbox-row">
                    <input type="checkbox" name="hapus_foto" value="1"> Hapus foto ini
                </label>
                <p class="form-hint" style="margin-top:10px;">Atau pilih file baru di bawah untuk mengganti foto.</p>
            <?php } ?>
            <input type="file" name="foto" accept="image/jpeg,image/png,image/webp">
            <p class="form-hint">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</p>

            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <a href="aspirasi_tampil.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
