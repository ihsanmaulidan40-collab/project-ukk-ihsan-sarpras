<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

require_once __DIR__ . '/config/koneksi.php';

$active = 'admin';
$error  = "";

if (isset($_POST['simpan'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $cek = mysqli_prepare($koneksi, "SELECT username FROM admin WHERE username = ?");
    mysqli_stmt_bind_param($cek, "s", $username);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO admin (username, password) VALUES (?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: admin_tampil.php");
            exit;
        } else {
            $error = "Gagal menambahkan data: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Admin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="content">

    <div class="page-header">
        <h1>Tambah Admin</h1>
    </div>

    <div class="panel">

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST">

            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <div class="form-actions">
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <a href="admin_tampil.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
