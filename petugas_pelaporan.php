<?php
require_once __DIR__ . '/includes/auth.php';
require_role('petugas');

require_once __DIR__ . '/config/koneksi.php';

$active = 'pelaporan';

$query = mysqli_query($koneksi, "SELECT * FROM input_aspirasi ORDER BY
    FIELD(status, 'Menunggu', 'Proses', 'Selesai'), id_pelaporan ASC");

function status_class_pp($status)
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
    <title>Kelola Pelaporan - Petugas</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Kelola Pelaporan Sarpras</h1>
        <p>Tindak lanjuti laporan kerusakan/kebutuhan sarpras dari siswa</p>
    </div>

    <div class="panel">

        <div class="table-wrapper">
            <table>
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>NIS</th>
                    <th>ID Kategori</th>
                    <th>Lokasi</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>

                <?php $no = 1; while ($d = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['id_pelaporan']) ?></td>
                    <td><?= htmlspecialchars($d['nis']) ?></td>
                    <td><?= htmlspecialchars($d['id_kategori']) ?></td>
                    <td><?= htmlspecialchars($d['lokasi']) ?></td>
                    <td><?= htmlspecialchars($d['ket']) ?></td>
                    <td>
                        <span class="status-badge <?= status_class_pp($d['status']) ?>">
                            <?= htmlspecialchars($d['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="petugas_pelaporan_edit.php?id_pelaporan=<?= urlencode($d['id_pelaporan']) ?>" class="btn btn-edit">Proses</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

    </div>

</div>

</body>
</html>
