<?php
/**
 * Helper upload foto bukti aspirasi.
 *
 * Foto disimpan di folder uploads/aspirasi/ dengan nama acak, dan hanya
 * NAMA FILE-nya yang disimpan di kolom aspirasi.foto.
 *
 * Aturan: hanya JPG / PNG / WEBP, maksimal 2 MB. Jenis file dicek dari ISI
 * file (bukan dari ekstensi/nama yang dikirim browser), supaya file .php
 * yang diganti nama jadi .jpg tetap ditolak.
 */

const FOTO_BUKTI_DIR = __DIR__ . '/../uploads/aspirasi/';
const FOTO_BUKTI_URL = 'uploads/aspirasi/';
const FOTO_BUKTI_MAX = 2097152; // 2 MB

/**
 * Simpan foto dari $_FILES['foto'].
 *
 * Return:
 *   string -> nama file baru (berhasil disimpan)
 *   null   -> tidak ada file yang dipilih (bukan error, foto bersifat opsional)
 *   false  -> gagal, alasannya ada di $error
 */
function simpan_foto_bukti($file, &$error = '')
{
    $error = '';

    if (!is_array($file) || !isset($file['error'])) {
        return null;
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        $error = "Ukuran foto terlalu besar. Maksimal 2 MB.";
        return false;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Upload foto gagal (kode error " . (int) $file['error'] . ").";
        return false;
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        $error = "File upload tidak valid.";
        return false;
    }

    if ($file['size'] > FOTO_BUKTI_MAX) {
        $error = "Ukuran foto terlalu besar. Maksimal 2 MB.";
        return false;
    }

    // Cek jenis file dari isinya
    $info = @getimagesize($file['tmp_name']);
    $tipe_diizinkan = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_WEBP => 'webp',
    ];

    if ($info === false || !isset($tipe_diizinkan[$info[2]])) {
        $error = "File harus berupa gambar JPG, PNG, atau WEBP.";
        return false;
    }

    if (!is_dir(FOTO_BUKTI_DIR) && !mkdir(FOTO_BUKTI_DIR, 0755, true)) {
        $error = "Folder uploads/aspirasi tidak bisa dibuat. Cek izin folder.";
        return false;
    }

    $nama_file = 'bukti_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $tipe_diizinkan[$info[2]];

    if (!move_uploaded_file($file['tmp_name'], FOTO_BUKTI_DIR . $nama_file)) {
        $error = "Gagal menyimpan foto ke server. Cek izin folder uploads/aspirasi.";
        return false;
    }

    return $nama_file;
}

/**
 * Hapus file foto dari disk (aman dipanggil dengan nilai kosong).
 */
function hapus_foto_bukti($nama_file)
{
    if ($nama_file === null || $nama_file === '') {
        return;
    }
    $path = FOTO_BUKTI_DIR . basename($nama_file); // basename() cegah path traversal
    if (is_file($path)) {
        @unlink($path);
    }
}

/**
 * URL foto untuk dipakai di <img src> / <a href>.
 */
function url_foto_bukti($nama_file)
{
    return FOTO_BUKTI_URL . rawurlencode(basename($nama_file));
}

/**
 * Thumbnail (klik untuk buka ukuran penuh) untuk kolom tabel.
 * Kalau tidak ada foto, tampil tanda "-".
 */
function tampil_foto_bukti($nama_file)
{
    if ($nama_file === null || $nama_file === '') {
        return '-';
    }
    $url = htmlspecialchars(url_foto_bukti($nama_file));
    return '<a href="' . $url . '" target="_blank" rel="noopener">'
         . '<img class="thumb" src="' . $url . '" alt="Foto bukti"></a>';
}

/**
 * True kalau request POST dibuang PHP karena melebihi post_max_size
 * (kalau ini terjadi, $_POST dan $_FILES kosong sama sekali).
 */
function post_melebihi_batas()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST'
        && empty($_POST)
        && empty($_FILES)
        && !empty($_SERVER['CONTENT_LENGTH']);
}
