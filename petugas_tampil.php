<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

$active = 'petugas';

$query = mysqli_query($koneksi, "SELECT * FROM petugas ORDER BY username ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Petugas</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Data Petugas</h1>
        <p>Akun petugas yang bisa mengelola Aspirasi &amp; Pelaporan</p>
    </div>

    <div class="panel">

        <div class="toolbar">
            <a href="petugas_tambah.php" class="btn btn-primary">+ Tambah Petugas</a>
        </div>

        <div class="table-wrapper">
            <table>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Aksi</th>
                </tr>

                <?php $no = 1; while ($d = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['username']) ?></td>
                    <td><?= htmlspecialchars($d['nama']) ?></td>
                    <td>
                        <a href="petugas_edit.php?username=<?= urlencode($d['username']) ?>" class="btn btn-edit">Edit</a>
                        <a href="petugas_hapus.php?username=<?= urlencode($d['username']) ?>" class="btn btn-hapus"
                           onclick="return confirm('Yakin ingin menghapus petugas ini?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <p style="margin-top:15px; font-size:13px; color:#64748b;">
            Catatan: password petugas tidak ditampilkan demi keamanan.
        </p>

    </div>

</div>

</body>
</html>
