<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

$active = 'kategori';

$query = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY id_kategori ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kategori</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Data Kategori</h1>
        <p>Kategori sarana &amp; prasarana</p>
    </div>

    <div class="panel">

        <div class="toolbar">
            <a href="kategori_tambah.php" class="btn btn-primary">+ Tambah Kategori</a>
        </div>

        <div class="table-wrapper">
            <table>
                <tr>
                    <th>No</th>
                    <th>ID Kategori</th>
                    <th>Keterangan Kategori</th>
                    <th>Aksi</th>
                </tr>

                <?php $no = 1; while ($d = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['id_kategori']) ?></td>
                    <td><?= htmlspecialchars($d['ket_kategori']) ?></td>
                    <td>
                        <a href="kategori_edit.php?id_kategori=<?= urlencode($d['id_kategori']) ?>" class="btn btn-edit">Edit</a>
                        <a href="kategori_hapus.php?id_kategori=<?= urlencode($d['id_kategori']) ?>" class="btn btn-hapus"
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
