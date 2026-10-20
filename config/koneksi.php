<?php
/**
 * Koneksi database terpusat.
 * Semua file lain cukup: require_once __DIR__ . '/config/koneksi.php';
 */

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_ihsan_paket3_root";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");
