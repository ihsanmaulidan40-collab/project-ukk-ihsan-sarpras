<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/upload.php';

$active = 'aspirasi';

$query = mysqli_query($koneksi, "SELECT * FROM aspirasi ORDER BY id_aspirasi ASC");

function status_class($status)
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
    <title>Data Aspirasi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Data Aspirasi</h1>
    </div>

    <div class="panel">

        <div class="toolbar">
            <a href="aspirasi_tambah.php" class="btn btn-primary">+ Tambah Aspirasi</a>
        </div>

        <div class="table-wrapper">
            <table>
                <tr>
                    <th>No</th>
                    <th>ID Aspirasi</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th>ID Kategori</th>
                    <th>Foto Bukti</th>
                    <th>Feedback</th>
                    <th>NIS Terkait</th>
                    <th>Aksi</th>
                </tr>

                <?php $no = 1; while ($d = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['id_aspirasi']) ?></td>
                    <td><?= htmlspecialchars($d['nama']) ?></td>
                    <td><?= htmlspecialchars($d['kelas']) ?></td>
                    <td>
                        <span class="status-badge <?= status_class($d['status']) ?>">
                            <?= htmlspecialchars($d['status']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($d['id_kategori']) ?></td>
                    <td><?= tampil_foto_bukti($d['foto'] ?? null) ?></td>
                    <td><?= htmlspecialchars($d['feedback'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($d['nis'] ?? '-') ?></td>
                    <td>
                        <a href="aspirasi_edit.php?id_aspirasi=<?= urlencode($d['id_aspirasi']) ?>" class="btn btn-edit">Edit</a>
                        <a href="aspirasi_hapus.php?id_aspirasi=<?= urlencode($d['id_aspirasi']) ?>" class="btn btn-hapus"
                           onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

    </div>

</div>

</body>
</html>
