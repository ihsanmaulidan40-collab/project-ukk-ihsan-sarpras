<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

$active = 'admin';

$query = mysqli_query($koneksi, "SELECT * FROM admin ORDER BY username ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Admin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Data Admin</h1>
        <p>Akun yang bisa login ke sistem ini</p>
    </div>

    <div class="panel">

        <div class="toolbar">
            <a href="admin_tambah.php" class="btn btn-primary">+ Tambah Admin</a>
        </div>

        <div class="table-wrapper">
            <table>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Aksi</th>
                </tr>

                <?php $no = 1; while ($d = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['username']) ?></td>
                    <td>
                        <a href="admin_edit.php?username=<?= urlencode($d['username']) ?>" class="btn btn-edit">Edit</a>
                        <a href="admin_hapus.php?username=<?= urlencode($d['username']) ?>" class="btn btn-hapus"
                           onclick="return confirm('Yakin ingin menghapus admin ini?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <p style="margin-top:15px; font-size:13px; color:#64748b;">
            Catatan: password admin tidak ditampilkan demi keamanan.
        </p>

    </div>

</div>

</body>
</html>
