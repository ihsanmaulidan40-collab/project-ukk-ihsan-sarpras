<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/koneksi.php';

// Kalau sudah login, langsung ke dashboard sesuai rolenya (bukan sebaliknya)
if (!empty($_SESSION['login'])) {
    header("Location: " . dashboard_url_for_role($_SESSION['role'] ?? ''));
    exit;
}

$error = "";
$role_dipilih = $_POST['role'] ?? ($_GET['role'] ?? 'admin');
if (!in_array($role_dipilih, ['admin', 'petugas', 'siswa'], true)) {
    $role_dipilih = 'admin';
}
if (($_GET['daftar'] ?? '') === 'sukses' && !isset($_POST['role'])) {
    $role_dipilih = 'siswa';
}
$sukses = (($_GET['daftar'] ?? '') === 'sukses')
    ? "Pendaftaran berhasil! Silakan login memakai NIS dan password Anda."
    : "";

if (isset($_POST['login'])) {

    $role     = $_POST['role'] ?? '';
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!in_array($role, ['admin', 'petugas', 'siswa'], true)) {
        $error = "Silakan pilih login sebagai Admin, Petugas, atau Siswa.";
    } elseif ($username === '' || $password === '') {
        $error = "Username/NIS dan password wajib diisi!";
    } else {

        // Tiap role punya tabelnya sendiri: admin -> admin, petugas -> petugas,
        // siswa -> siswa (username = NIS).
        if ($role === 'admin') {
            $stmt = mysqli_prepare($koneksi, "SELECT * FROM admin WHERE username = ? LIMIT 1");
        } elseif ($role === 'petugas') {
            $stmt = mysqli_prepare($koneksi, "SELECT * FROM petugas WHERE username = ? LIMIT 1");
        } else {
            $stmt = mysqli_prepare($koneksi, "SELECT * FROM siswa WHERE nis = ? LIMIT 1");
        }

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);

        if ($hasil && mysqli_num_rows($hasil) > 0) {

            $data = mysqli_fetch_assoc($hasil);

            if (password_cocok($password, $data['password'])) {

                $_SESSION['login'] = true;
                $_SESSION['role']  = $role;

                if ($role === 'admin') {
                    $_SESSION['username'] = $data['username'];
                    $_SESSION['nama']     = $data['username'];
                } elseif ($role === 'petugas') {
                    $_SESSION['username'] = $data['username'];
                    $_SESSION['nama']     = $data['nama'];
                } else { // siswa
                    $_SESSION['nis']   = $data['nis'];
                    $_SESSION['nama']  = $data['nama'];
                    $_SESSION['kelas'] = $data['kelas'];
                }

                header("Location: " . dashboard_url_for_role($role));
                exit;
            } else {
                $error = "Username/NIS atau password salah!";
            }

        } else {
            $error = "Username/NIS atau password salah!";
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pengadaan Sarpras</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-box">

        <h2>LOGIN</h2>

        <?php if ($error !== "") { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <?php if ($sukses !== "" && $error === "") { ?>
            <div class="success"><?= htmlspecialchars($sukses) ?></div>
        <?php } ?>

        <form method="POST">

            <label>Login sebagai</label>
            <select name="role" required>
                <option value="admin"   <?= $role_dipilih === 'admin'   ? 'selected' : '' ?>>Admin</option>
                <option value="siswa"   <?= $role_dipilih === 'siswa'   ? 'selected' : '' ?>>Siswa</option>
            </select>

            <label id="label-username">Username</label>
            <input type="text" name="username" id="input-username" required autofocus>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit" name="login" class="btn btn-primary" style="width:100%; margin-top:20px;">
                Login
            </button>

        </form>

        <p style="text-align:center; margin-top:14px; font-size:12px; color:#64748b;">
            Siswa login memakai <b>NIS</b> sebagai username.
        </p>

        <p class="auth-link">
            Siswa belum punya akun? <a href="daftar.php">Daftar di sini</a>
        </p>

    </div>
</div>

<script>
    // Ganti label "Username" jadi "NIS" saat role Siswa dipilih, sekadar bantuan visual.
    const roleSelect = document.querySelector('select[name="role"]');
    const labelUsername = document.getElementById('label-username');
    const inputUsername = document.getElementById('input-username');

    function updateLabel() {
        if (roleSelect.value === 'siswa') {
            labelUsername.textContent = 'NIS';
            inputUsername.placeholder = 'Contoh: 24010012';
        } else {
            labelUsername.textContent = 'Username';
            inputUsername.placeholder = '';
        }
    }
    roleSelect.addEventListener('change', updateLabel);
    updateLabel();
</script>

</body>
</html>
