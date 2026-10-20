-- =========================================================
-- Database: ihsan_12rpl1_paket3_root
-- Proyek  : Pengadaan Sarpras
-- Versi   : Multi-role (Admin, Petugas, Siswa)
-- =========================================================

CREATE DATABASE IF NOT EXISTS `ihsan_12rpl1_paket3_root`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_general_ci;

USE `ihsan_12rpl1_paket3_root`;

-- =========================================================
-- Tabel admin (login sebagai Admin)
-- =========================================================
CREATE TABLE IF NOT EXISTS `admin` (
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`username`)
) ENGINE=InnoDB;

-- Akun default: username = admin, password = admin123
-- >>> SILAKAN GANTI PASSWORD INI SETELAH LOGIN PERTAMA KALI <<<
INSERT INTO `admin` (`username`, `password`)
VALUES ('admin', 'admin123')
ON DUPLICATE KEY UPDATE `username` = `username`;

-- =========================================================
-- Tabel petugas (login sebagai Petugas, dikelola oleh Admin)
-- =========================================================
CREATE TABLE IF NOT EXISTS `petugas` (
    `username` VARCHAR(50)  NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `nama`     VARCHAR(100) NOT NULL,
    PRIMARY KEY (`username`)
) ENGINE=InnoDB;

-- Akun default: username = petugas, password = petugas123
-- >>> SILAKAN GANTI PASSWORD INI SETELAH LOGIN PERTAMA KALI <<<
INSERT INTO `petugas` (`username`, `password`, `nama`)
VALUES ('petugas', 'petugas123', 'Petugas Sarpras')
ON DUPLICATE KEY UPDATE `username` = `username`;

-- =========================================================
-- Tabel siswa (login sebagai Siswa, NIS dipakai sebagai username)
-- =========================================================
CREATE TABLE IF NOT EXISTS `siswa` (
    `nis`      VARCHAR(20)  NOT NULL,
    `password` VARCHAR(255) NOT NULL DEFAULT '',
    `nama`     VARCHAR(100) NOT NULL,
    `kelas`    VARCHAR(20)  NOT NULL,
    `jurusan`  VARCHAR(50)  NOT NULL,
    PRIMARY KEY (`nis`)
) ENGINE=InnoDB;

-- =========================================================
-- Tabel kategori (independen)
-- =========================================================
CREATE TABLE IF NOT EXISTS `kategori` (
    `id_kategori`  INT(11)     NOT NULL,
    `ket_kategori` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB;

-- =========================================================
-- Tabel aspirasi (FK ke kategori, FK opsional ke siswa)
-- `nis` NULL diperbolehkan supaya Admin tetap bisa input aspirasi
-- manual (mis. hasil survei) tanpa terikat akun siswa tertentu.
-- `foto` = nama file foto bukti (disimpan di folder uploads/aspirasi/), opsional.
-- =========================================================
CREATE TABLE IF NOT EXISTS `aspirasi` (
    `id_aspirasi` INT(11)      NOT NULL,
    `nama`        VARCHAR(100) NOT NULL,
    `kelas`       VARCHAR(20)  NOT NULL,
    `status`      ENUM('Menunggu', 'Proses', 'Selesai') NOT NULL DEFAULT 'Menunggu',
    `id_kategori` INT(11)      NOT NULL,
    `feedback`    TINYINT(1)   NULL,
    `nis`         VARCHAR(20)  NULL,
    `foto`        VARCHAR(255) NULL,
    PRIMARY KEY (`id_aspirasi`),
    KEY `fk_aspirasi_kategori` (`id_kategori`),
    KEY `fk_aspirasi_siswa` (`nis`),
    CONSTRAINT `fk_aspirasi_kategori`
        FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_aspirasi_siswa`
        FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================
-- Tabel input_aspirasi / pelaporan sarpras (FK ke siswa & kategori)
-- Kolom `status` ditambahkan supaya Petugas bisa memproses laporan.
-- =========================================================
CREATE TABLE IF NOT EXISTS `input_aspirasi` (
    `id_pelaporan` INT(11)      NOT NULL,
    `nis`          VARCHAR(20)  NOT NULL,
    `id_kategori`  INT(11)      NOT NULL,
    `lokasi`       VARCHAR(100) NOT NULL,
    `ket`          VARCHAR(255) NOT NULL,
    `status`       ENUM('Menunggu', 'Proses', 'Selesai') NOT NULL DEFAULT 'Menunggu',
    PRIMARY KEY (`id_pelaporan`),
    KEY `fk_pelaporan_siswa` (`nis`),
    KEY `fk_pelaporan_kategori` (`id_kategori`),
    CONSTRAINT `fk_pelaporan_siswa`
        FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_pelaporan_kategori`
        FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;
