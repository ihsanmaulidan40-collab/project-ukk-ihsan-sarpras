<?php
require_once __DIR__ . '/includes/auth.php';
require_role('petugas');

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/upload.php';

$active = 'aspirasi';

$query = mysqli_query($koneksi, "SELECT * FROM aspirasi ORDER BY
    FIELD(status, 'Menunggu', 'Proses', 'Selesai'), id_aspirasi ASC");

function status_class_p($status)
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
    <title>Kelola Aspirasi - Petugas</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Kelola Aspirasi</h1>
        <p>Ubah status dan beri feedback untuk aspirasi yang masuk dari siswa</p>
    </div>

    <div class="panel">

        <div class="table-wrapper">
            <table>
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>ID Kategori</th>
                    <th>Foto Bukti</th>
                    <th>Status</th>
                    <th>Feedback</th>
                    <th>Aksi</th>
                </tr>

                <?php $no = 1; while ($d = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['id_aspirasi']) ?></td>
                    <td><?= htmlspecialchars($d['nama']) ?></td>
                    <td><?= htmlspecialchars($d['kelas']) ?></td>
                    <td><?= htmlspecialchars($d['id_kategori']) ?></td>
                    <td><?= tampil_foto_bukti($d['foto'] ?? null) ?></td>
                    <td>
                        <span class="status-badge <?= status_class_p($d['status']) ?>">
                            <?= htmlspecialchars($d['status']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($d['feedback'] ?? '-') ?></td>
                    <td>
                        <a href="petugas_aspirasi_edit.php?id_aspirasi=<?= urlencode($d['id_aspirasi']) ?>" class="btn btn-edit">Proses</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

    </div>

</div>

</body>
</html>
