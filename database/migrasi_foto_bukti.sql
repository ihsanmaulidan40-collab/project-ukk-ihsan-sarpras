-- =========================================================
-- Migrasi: tambah kolom foto bukti pada tabel aspirasi
-- Jalankan SEKALI saja kalau database sudah terlanjur di-import
-- dari versi sebelumnya (data lama tetap aman).
-- Kalau import baru dari ihsan_12rpl1_paket3_root.sql, TIDAK perlu.
-- =========================================================
USE `ihsan_12rpl1_paket3_root`;

ALTER TABLE `aspirasi`
    ADD COLUMN `foto` VARCHAR(255) NULL AFTER `nis`;
