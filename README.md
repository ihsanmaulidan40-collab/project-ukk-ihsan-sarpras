# Pengadaan Sarpras — Multi-Role (Admin, Petugas, Siswa)

Sistem Pengadaan Sarpras dengan tiga peran login: **Admin**, **Petugas**, dan
**Siswa**, masing-masing dengan dashboard dan menu sendiri.

## Struktur folder

```
Pengadaan_Sarpras_ihsan/
├── config/koneksi.php            # satu-satunya file koneksi database
├── includes/auth.php             # session_start(), require_login(), require_role()
├── includes/sidebar.php          # sidebar dinamis sesuai role yang login
├── assets/style.css              # satu stylesheet gabungan
├── database/ihsan_12rpl1_paket3_root.sql
├── index.php                     # gerbang: ke dashboard sesuai role, atau ke login
├── login.php / logout.php        # login satu pintu, pilih role di dropdown
├── daftar.php                    # pendaftaran akun Siswa (publik, tanpa login)
├── includes/upload.php           # helper upload foto bukti aspirasi
├── uploads/aspirasi/             # tempat foto bukti (ada .htaccess pemblokir script)
│
├── dashboard.php                 # dashboard Admin
├── siswa_tampil.php / siswa_tambah.php / siswa_edit.php / siswa_hapus.php     (Admin -> kelola akun Siswa)
├── kategori_tampil.php / kategori_tambah.php / kategori_edit.php / kategori_hapus.php  (Admin)
├── aspirasi_tampil.php / aspirasi_tambah.php / aspirasi_edit.php / aspirasi_hapus.php  (Admin)
├── pelaporan_tampil.php / pelaporan_tambah.php / pelaporan_edit.php / pelaporan_hapus.php  (Admin)
├── admin_tampil.php / admin_tambah.php / admin_edit.php / admin_hapus.php     (Admin -> kelola akun Admin)
├── petugas_tampil.php / petugas_tambah.php / petugas_edit.php / petugas_hapus.php  (Admin -> kelola akun Petugas)
│
├── petugas_dashboard.php         # dashboard Petugas
├── petugas_aspirasi.php / petugas_aspirasi_edit.php     (Petugas -> proses status & feedback Aspirasi)
├── petugas_pelaporan.php / petugas_pelaporan_edit.php   (Petugas -> proses status Pelaporan)
├── petugas_kategori.php / petugas_siswa.php             (Petugas -> lihat data referensi, read-only)
├── petugas_akun.php              (Petugas -> ganti nama & password sendiri)
│
├── siswa_dashboard.php           # dashboard Siswa
├── siswa_aspirasi.php / siswa_aspirasi_tambah.php / siswa_aspirasi_edit.php / siswa_aspirasi_hapus.php
├── siswa_pelaporan.php / siswa_pelaporan_tambah.php / siswa_pelaporan_edit.php / siswa_pelaporan_hapus.php
└── siswa_akun.php                (Siswa -> ganti password sendiri)
```

`pelaporan_*` = tabel `input_aspirasi` (laporan kerusakan/kebutuhan sarpras dari siswa).

## Cara menjalankan

1. Import `database/ihsan_12rpl1_paket3_root.sql` ke MySQL/MariaDB (lewat phpMyAdmin
   atau `mysql -u root < database/ihsan_12rpl1_paket3_root.sql`). File ini otomatis
   membuat database `ihsan_12rpl1_paket3_root`, semua tabel, relasi FK, dan akun
   default untuk Admin & Petugas.
2. Letakkan folder ini di `htdocs`/`www` server lokal (XAMPP/Laragon).
3. Buka `http://localhost/Pengadaan_Sarpras_ihsan/`.
4. Login dengan salah satu akun default (pilih role di dropdown login):

   | Role    | Username / NIS | Password      |
   |---------|-----------------|---------------|
   | Admin   | `admin`         | `admin123`    |
   | Petugas | `petugas`       | `petugas123`  |
   | Siswa   | *(dibuat Admin lewat menu Data Siswa)* | *(diisi Admin saat membuat akun siswa)* |

   Segera ganti password default ini setelah login pertama kali.
5. Admin mengisi dulu **Data Siswa** (sekaligus mengisi password login siswa) dan
   **Data Kategori** sebelum siswa bisa login dan mengirim Aspirasi/Pelaporan,
   karena keduanya butuh NIS/ID Kategori yang sudah ada (relasi foreign key).

## Fitur baru

### 1. Daftar akun Siswa (`daftar.php`)

- Siswa bisa membuat akun sendiri dari link **"Daftar di sini"** di halaman login
  (tanpa harus menunggu Admin). Isian: NIS, nama, kelas, jurusan, password.
- Validasi: semua kolom wajib, NIS 3-20 karakter huruf/angka, password minimal
  6 karakter dan harus sama dengan konfirmasinya, NIS tidak boleh dobel.
- Password akun hasil pendaftaran disimpan **ter-hash** (`password_hash()`).
  `login.php` dan `siswa_akun.php` memakai fungsi `password_cocok()` di
  `includes/auth.php` yang mengenali hash **maupun** teks biasa, jadi akun lama
  buatan Admin tetap bisa login seperti biasa.
- Admin tetap bisa menambah/mengedit/menghapus siswa lewat menu Data Siswa.

### 2. Foto bukti pada Aspirasi

- Kolom baru `aspirasi.foto` (nama file; file aslinya di `uploads/aspirasi/`).
- **Siswa**: bisa melampirkan foto saat Kirim Aspirasi; saat Edit bisa mengganti
  atau menghapus foto (selama status masih `Menunggu`).
- **Admin**: bisa menambah/mengganti/menghapus foto di Tambah & Edit Aspirasi.
- **Petugas**: melihat foto di daftar dan halaman Proses Aspirasi (read-only).
- Foto bersifat **opsional**. Format JPG/PNG/WEBP, maksimal 2 MB. Jenis file
  dicek dari isi file (bukan ekstensi), nama file diacak, dan folder upload
  dilindungi `.htaccess` supaya script tidak bisa dijalankan dari sana.
- Saat aspirasi dihapus atau fotonya diganti, file lama ikut dihapus dari disk.

### Kalau database sudah terlanjur di-import dari versi lama

Jalankan **sekali** `database/migrasi_foto_bukti.sql` (menambah kolom `foto`).
Kalau import baru dari `ihsan_12rpl1_paket3_root.sql`, tidak perlu.

Pastikan folder `uploads/aspirasi/` bisa ditulis oleh web server. Di XAMPP/Laragon
Windows biasanya sudah otomatis. Di Linux: `chmod 775 uploads/aspirasi`.
Batas upload PHP (`upload_max_filesize` / `post_max_size` di `php.ini`) sebaiknya
minimal 2M (default umumnya 2M/8M, sudah cukup).

## Alur tiap peran

- **Admin**: kelola semua data master (Siswa, Kategori), kelola akun Admin &
  Petugas, serta punya akses penuh CRUD ke Aspirasi & Pelaporan (termasuk hapus
  dan mengubah status kapan pun).
- **Petugas**: login pakai akun yang dibuatkan Admin. Melihat semua Aspirasi &
  Pelaporan yang masuk, lalu memproses status (`Menunggu` -> `Proses` ->
  `Selesai`) dan mengisi feedback pada Aspirasi. Tidak bisa menghapus data atau
  mengubah data master (Siswa/Kategori) — halaman tersebut read-only untuk
  Petugas. Bisa ganti nama & password akunnya sendiri lewat menu "Akun Saya".
- **Siswa**: login pakai **NIS** sebagai username. Bisa mengirim Aspirasi baru
  dan membuat Pelaporan kerusakan/kebutuhan sarpras baru (ID dibuat otomatis).
  Hanya bisa melihat, mengedit, atau membatalkan (hapus) miliknya **sendiri**,
  dan hanya selama statusnya masih `Menunggu` — begitu Petugas mulai memproses
  (`Proses`/`Selesai`), data itu terkunci dari sisi siswa. Siswa juga bisa
  ganti password sendiri lewat menu "Akun Saya" (nama/kelas/jurusan hanya bisa
  diubah oleh Admin).

## Bug utama yang diperbaiki dari kode lama

1. **Login tidak pernah dicek.** Versi lama punya `header('location:
   dashboard.php'); exit;` di baris paling atas, SEBELUM proses cek
   username/password — jadi siapa pun otomatis masuk ke dashboard tanpa
   login. Logic itu dihapus.
2. **Login mengecek tabel yang salah.** File `login.php` yang diupload
   sebelumnya query `SELECT * FROM users`, padahal tabel yang ada isinya
   `admin` — jadi login Admin sebenarnya **selalu gagal** ("Username atau
   password salah!") walau kredensial benar. Sekarang setiap role query ke
   tabelnya masing-masing (`admin`, `petugas`, `siswa`).
3. **Konflik nama file (case-sensitive)** dan **file korup berisi markdown**
   dari versi yang lebih lama — sudah tidak ada lagi di versi ini.
4. **Tidak ada pemisahan peran.** Semula hanya ada satu jenis login (Admin)
   yang bisa mengakses semua modul. Sekarang ada 3 role dengan menu, dashboard,
   dan batasan akses (`require_role()`) masing-masing.
5. **SQL Injection** — semua query pakai prepared statement
   (`mysqli_prepare` + `bind_param`).
6. **XSS** — semua output ke HTML lewat `htmlspecialchars()`.
7. **Validasi relasi (foreign key) di level aplikasi** sebelum menyimpan
   Aspirasi/Pelaporan (NIS & ID Kategori harus sudah ada).
8. **Hapus data yang masih dipakai relasi lain** dicegah/di-cascade dengan
   benar; Admin tidak bisa menghapus akunnya sendiri atau menghapus admin
   terakhir.
9. **Kategori tidak terpilih otomatis di form Edit** (`siswa_aspirasi_edit.php`,
   `siswa_pelaporan_edit.php`, `pelaporan_edit.php`). Perbandingan `===` antara
   string dan integer selalu salah, sehingga dropdown selalu kembali ke pilihan
   pertama dan bisa mengubah kategori tanpa sengaja. Sekarang dibandingkan
   sebagai string.

## Catatan keamanan

Password akun **Admin, Petugas, dan Siswa buatan Admin** masih disimpan sebagai teks biasa (mengikuti gaya kode aslinya,
supaya konsisten dengan cara kerja menu "Data Admin"/"Data Petugas"/"Data
Siswa"). Untuk penggunaan di luar tugas sekolah, ganti jadi `password_hash()`
/ `password_verify()` di semua tempat yang memproses password (`login.php`,
`admin_tambah.php`, `admin_edit.php`, `petugas_tambah.php`, `petugas_edit.php`,
`siswa_tambah.php`, `siswa_edit.php`, `siswa_akun.php`, `petugas_akun.php`).
