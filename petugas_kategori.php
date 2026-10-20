<?php
require_once __DIR__ . '/includes/auth.php';
require_role('petugas');

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
        <p>Referensi kategori sarana &amp; prasarana (hanya lihat)</p>
    </div>

    <div class="panel">
        <div class="table-wrapper">
            <table>
                <tr>
                    <th>No</th>
                    <th>ID Kategori</th>
                    <th>Keterangan Kategori</th>
                </tr>

                <?php $no = 1; while ($d = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['id_kategori']) ?></td>
                    <td><?= htmlspecialchars($d['ket_kategori']) ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>
