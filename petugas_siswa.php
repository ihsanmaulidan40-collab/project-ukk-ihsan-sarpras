<?php
require_once __DIR__ . '/includes/auth.php';
require_role('petugas');

require_once __DIR__ . '/config/koneksi.php';

$active = 'siswa';

$query = mysqli_query($koneksi, "SELECT nis, nama, kelas, jurusan FROM siswa ORDER BY nis ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Data Siswa</h1>
        <p>Referensi data siswa (hanya lihat)</p>
    </div>

    <div class="panel">
        <div class="table-wrapper">
            <table>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                </tr>

                <?php $no = 1; while ($d = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['nis']) ?></td>
                    <td><?= htmlspecialchars($d['nama']) ?></td>
                    <td><?= htmlspecialchars($d['kelas']) ?></td>
                    <td><?= htmlspecialchars($d['jurusan']) ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>
