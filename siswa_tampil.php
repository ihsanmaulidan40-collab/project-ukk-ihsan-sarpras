<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

$active = 'siswa';

$query = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY nis ASC");
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
        <p>Kelola data siswa</p>
    </div>

    <div class="panel">

        <div class="toolbar">
            <a href="siswa_tambah.php" class="btn btn-primary">+ Tambah Siswa</a>
        </div>

        <div class="table-wrapper">
            <table>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Aksi</th>
                </tr>

                <?php $no = 1; while ($d = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['nis']) ?></td>
                    <td><?= htmlspecialchars($d['nama']) ?></td>
                    <td><?= htmlspecialchars($d['kelas']) ?></td>
                    <td><?= htmlspecialchars($d['jurusan']) ?></td>
                    <td>
                        <a href="siswa_edit.php?nis=<?= urlencode($d['nis']) ?>" class="btn btn-edit">Edit</a>
                        <a href="siswa_hapus.php?nis=<?= urlencode($d['nis']) ?>" class="btn btn-hapus"
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
