<?php
require_once __DIR__ . '/includes/auth.php';
require_role('petugas');

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

// Petugas hanya boleh mengubah status & feedback, bukan data inti aspirasi.
if (isset($_POST['update'])) {

    $status   = $_POST['status'];
    $feedback = $_POST['feedback'] !== '' ? (int) $_POST['feedback'] : null;

    $update = mysqli_prepare(
        $koneksi,
        "UPDATE aspirasi SET status = ?, feedback = ? WHERE id_aspirasi = ?"
    );
    mysqli_stmt_bind_param($update, "sis", $status, $feedback, $id_aspirasi);

    if (mysqli_stmt_execute($update)) {
        header("Location: petugas_aspirasi.php");
        exit;
    } else {
        $error = "Gagal mengubah data: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Aspirasi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Proses Aspirasi #<?= htmlspecialchars($data['id_aspirasi']) ?></h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <p><b>Nama:</b> <?= htmlspecialchars($data['nama']) ?></p>
        <p><b>Kelas:</b> <?= htmlspecialchars($data['kelas']) ?></p>
        <p><b>ID Kategori:</b> <?= htmlspecialchars($data['id_kategori']) ?></p>
        <p><b>Foto Bukti:</b></p>
        <?php if (!empty($data['foto'])) { ?>
            <a href="<?= htmlspecialchars(url_foto_bukti($data['foto'])) ?>" target="_blank" rel="noopener">
                <img class="foto-preview" src="<?= htmlspecialchars(url_foto_bukti($data['foto'])) ?>" alt="Foto bukti">
            </a>
        <?php } else { ?>
            <p style="color:#94a3b8; margin-bottom:10px;">Tidak ada foto bukti.</p>
        <?php } ?>

        <form method="POST">

            <label>Status</label>
            <select name="status" required>
                <?php foreach (['Menunggu', 'Proses', 'Selesai'] as $opt) { ?>
                    <option value="<?= $opt ?>" <?= $data['status'] === $opt ? 'selected' : '' ?>>
                        <?= $opt ?>
                    </option>
                <?php } ?>
            </select>

            <label>Feedback (1-5)</label>
            <input type="number" name="feedback" min="1" max="5" value="<?= htmlspecialchars($data['feedback'] ?? '') ?>">

            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Simpan</button>
                <a href="petugas_aspirasi.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
