<?php
require_once __DIR__ . '/includes/auth.php';
require_role('siswa');

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/upload.php';

$active = 'aspirasi';
$nis    = $_SESSION['nis'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM aspirasi WHERE nis = ? ORDER BY id_aspirasi DESC");
mysqli_stmt_bind_param($stmt, "s", $nis);
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);

function status_class_sa($status)
{
    switch ($status) {
        case 'Menunggu': return 'status-menunggu';
        case 'Proses':   return 'status-proses';
        case 'Selesai':  return 'status-selesai';
        default:         return '';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aspirasi Saya</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Aspirasi Saya</h1>
        <p>Aspirasi/masukan yang sudah Anda kirim</p>
    </div>

    <div class="panel">

        <div class="toolbar">
            <a href="siswa_aspirasi_tambah.php" class="btn btn-primary">+ Kirim Aspirasi</a>
        </div>

        <div class="table-wrapper">
            <table>
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>ID Kategori</th>
                    <th>Status</th>
                    <th>Foto Bukti</th>
                    <th>Feedback</th>
                    <th>Aksi</th>
                </tr>

                <?php if (mysqli_num_rows($query) === 0) { ?>
                <tr><td colspan="7">Belum ada aspirasi yang Anda kirim.</td></tr>
                <?php }
                $no = 1; while ($d = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['id_aspirasi']) ?></td>
                    <td><?= htmlspecialchars($d['id_kategori']) ?></td>
                    <td>
                        <span class="status-badge <?= status_class_sa($d['status']) ?>">
                            <?= htmlspecialchars($d['status']) ?>
                        </span>
                    </td>
                    <td><?= tampil_foto_bukti($d['foto'] ?? null) ?></td>
                    <td><?= htmlspecialchars($d['feedback'] ?? '-') ?></td>
                    <td>
                        <?php if ($d['status'] === 'Menunggu') { ?>
                            <a href="siswa_aspirasi_edit.php?id_aspirasi=<?= urlencode($d['id_aspirasi']) ?>" class="btn btn-edit">Edit</a>
                            <a href="siswa_aspirasi_hapus.php?id_aspirasi=<?= urlencode($d['id_aspirasi']) ?>" class="btn btn-hapus"
                               onclick="return confirm('Batalkan aspirasi ini?')">Hapus</a>
                        <?php } else { ?>
                            <span style="color:#94a3b8; font-size:13px;">Sudah diproses petugas</span>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

    </div>

</div>

</body>
</html>
