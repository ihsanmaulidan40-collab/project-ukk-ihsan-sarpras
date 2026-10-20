<?php
/**
 * Wajib di-include PALING ATAS di setiap halaman
 * (sebelum ada output HTML apa pun), sebelum koneksi.php.
 *
 * Role yang dikenal sistem: 'admin', 'petugas', 'siswa'.
 * Disimpan di $_SESSION['role'] setelah login berhasil (lihat login.php).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Panggil di halaman yang butuh login (peran apa pun).
 * Kalau belum login, langsung dilempar ke login.php.
 */
function require_login()
{
    if (empty($_SESSION['login'])) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Panggil di halaman yang hanya boleh diakses peran tertentu.
 * Contoh: require_role('admin');  atau  require_role(['admin', 'petugas']);
 *
 * Kalau belum login -> ke login.php.
 * Kalau login tapi rolenya tidak sesuai -> ditolak (403), diarahkan ke
 * dashboard sesuai rolenya sendiri, bukan malah ke halaman yang tidak boleh diakses.
 */
function require_role($allowed_roles)
{
    require_login();

    $allowed_roles = (array) $allowed_roles;
    $role          = $_SESSION['role'] ?? '';

    if (!in_array($role, $allowed_roles, true)) {
        http_response_code(403);
        $tujuan = dashboard_url_for_role($role);
        echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'>"
           . "<title>Akses Ditolak</title><link rel='stylesheet' href='assets/style.css'></head><body>"
           . "<div class='login-wrapper'><div class='login-box'>"
           . "<h2>Akses Ditolak</h2>"
           . "<div class='error'>Halaman ini bukan untuk peran akun Anda.</div>"
           . "<p style='text-align:center; margin-top:15px;'><a href='" . htmlspecialchars($tujuan) . "'>Kembali ke Dashboard</a></p>"
           . "</div></div></body></html>";
        exit;
    }
}

/**
 * URL dashboard yang sesuai untuk sebuah role.
 */
function dashboard_url_for_role($role)
{
    switch ($role) {
        case 'admin':   return 'dashboard.php';
        case 'petugas': return 'petugas_dashboard.php';
        case 'siswa':   return 'siswa_dashboard.php';
        default:        return 'login.php';
    }
}

/**
 * Label tampilan untuk sebuah role (dipakai di sidebar/header).
 */
function label_role($role)
{
    switch ($role) {
        case 'admin':   return 'Admin';
        case 'petugas': return 'Petugas';
        case 'siswa':   return 'Siswa';
        default:        return '';
    }
}

/**
 * Cocokkan password yang diketik dengan yang tersimpan di database.
 * Mendukung dua bentuk: hash (password_hash(), dipakai akun hasil pendaftaran
 * mandiri) dan teks biasa (akun lama buatan Admin), supaya akun lama tetap
 * bisa login tanpa perlu diubah.
 */
function password_cocok($input, $tersimpan)
{
    $tersimpan = (string) $tersimpan;
    if (password_get_info($tersimpan)['algo']) {
        return password_verify($input, $tersimpan);
    }
    return hash_equals($tersimpan, (string) $input);
}
