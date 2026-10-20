<?php
require_once __DIR__ . '/includes/auth.php';
require_role('siswa');

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/upload.php';

$active = 'aspirasi';
$error  = "";
$nis    = $_SESSION['nis'];

if (!isset($_GET['id_aspirasi'])) {
    die("ID Aspirasi tidak ditemukan.");
}
$id_aspirasi = $_GET['id_aspirasi'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM aspirasi WHERE id_aspirasi = ? AND nis = ?");
mysqli_stmt_bind_param($stmt, "ss", $id_aspirasi, $nis);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    die("Data aspirasi tidak ditemukan, atau bukan milik Anda.");
}

if ($data['status'] !== 'Menunggu') {
    die("Aspirasi yang sudah diproses petugas tidak bisa diubah lagi. "
        . "<a href='siswa_aspirasi.php'>Kembali</a>");
}

if (post_melebihi_batas()) {
    $error = "Ukuran file melebihi batas server. Gunakan foto maksimal 2 MB.";
}

if (isset($_POST['update'])) {

    $id_kategori = trim($_POST['id_kategori']);

    $cek_kategori = mysqli_prepare($koneksi, "SELECT id_kategori FROM kategori WHERE id_kategori = ?");
    mysqli_stmt_bind_param($cek_kategori, "s", $id_kategori);
    mysqli_stmt_execute($cek_kategori);
    mysqli_stmt_store_result($cek_kategori);

    if (mysqli_stmt_num_rows($cek_kategori) === 0) {
        $error = "ID Kategori tidak ditemukan!";
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
                "UPDATE aspirasi SET id_kategori = ?, foto = ? WHERE id_aspirasi = ? AND nis = ?"
            );
            mysqli_stmt_bind_param($update, "ssss", $id_kategori, $foto_akhir, $id_aspirasi, $nis);

            if (mysqli_stmt_execute($update)) {
                if ($foto_akhir !== $foto_lama) {
                    hapus_foto_bukti($foto_lama); // buang file lama yang sudah tidak dipakai
                }
                header("Location: siswa_aspirasi.php");
                exit;
            } else {
                hapus_foto_bukti($foto_baru); // batal simpan -> jangan sisakan file yatim
                $error = "Gagal mengubah data: " . mysqli_error($koneksi);
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
    <title>Edit Aspirasi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Edit Aspirasi #<?= htmlspecialchars($data['id_aspirasi']) ?></h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data">

            <label>Kategori</label>
            <select name="id_kategori" required>
                <?php while ($k = mysqli_fetch_assoc($data_kategori)) { ?>
                    <option value="<?= htmlspecialchars($k['id_kategori']) ?>"
                        <?= (string) $k['id_kategori'] === (string) $data['id_kategori'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['id_kategori']) ?> - <?= htmlspecialchars($k['ket_kategori']) ?>
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
                <button type="submit" name="update" class="btn btn-primary">Simpan</button>
                <a href="siswa_aspirasi.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
