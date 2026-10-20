<?php
require_once __DIR__ . '/includes/auth.php';
require_role('siswa');

require_once __DIR__ . '/config/koneksi.php';

$active = 'pelaporan';
$nis    = $_SESSION['nis'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM input_aspirasi WHERE nis = ? ORDER BY id_pelaporan DESC");
mysqli_stmt_bind_param($stmt, "s", $nis);
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);

function status_class_sp($status)
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
    <title>Pelaporan Saya</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Pelaporan Saya</h1>
        <p>Laporan kerusakan/kebutuhan sarpras yang sudah Anda kirim</p>
    </div>

    <div class="panel">

        <div class="toolbar">
            <a href="siswa_pelaporan_tambah.php" class="btn btn-primary">+ Buat Pelaporan</a>
        </div>

        <div class="table-wrapper">
            <table>
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>ID Kategori</th>
                    <th>Lokasi</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>

                <?php if (mysqli_num_rows($query) === 0) { ?>
                <tr><td colspan="7">Belum ada pelaporan yang Anda kirim.</td></tr>
                <?php }
                $no = 1; while ($d = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['id_pelaporan']) ?></td>
                    <td><?= htmlspecialchars($d['id_kategori']) ?></td>
                    <td><?= htmlspecialchars($d['lokasi']) ?></td>
                    <td><?= htmlspecialchars($d['ket']) ?></td>
                    <td>
                        <span class="status-badge <?= status_class_sp($d['status']) ?>">
                            <?= htmlspecialchars($d['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($d['status'] === 'Menunggu') { ?>
                            <a href="siswa_pelaporan_edit.php?id_pelaporan=<?= urlencode($d['id_pelaporan']) ?>" class="btn btn-edit">Edit</a>
                            <a href="siswa_pelaporan_hapus.php?id_pelaporan=<?= urlencode($d['id_pelaporan']) ?>" class="btn btn-hapus"
                               onclick="return confirm('Batalkan pelaporan ini?')">Hapus</a>
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
