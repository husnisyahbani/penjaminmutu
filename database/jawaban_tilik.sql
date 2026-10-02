-- ============================================================================
-- SIJAMU - Jawaban auditee untuk tiap butir tilik
--
-- Halaman detail audit auditee menampilkan pertanyaan beserta BUTIR TILIK
-- (baris `mutu_auditjawabdetail`) dan meminta jawaban untuk setiap butir.
-- Jawaban itu disimpan pada kolom `dtjwb_jawaban`.
--
-- Bila kolom ini belum ada, aplikasi tetap berjalan: jawaban disimpan pada
-- baris pertanyaan (`mutu_auditjawab`.`jwb_jawaban`) sehingga satu jawaban
-- dipakai untuk seluruh butir pada pertanyaan tersebut.
--
-- Lampiran jawaban menempel pada butir yang sama
-- (`mutu_lampiran`.`dtjwb_id`, lihat database/lampiran_lingkup.sql).
--
-- Cara pakai:
--   1. Impor berkas ini melalui phpMyAdmin (atau tab SQL).
--   2. Sesuaikan prefix `mutu_` bila berbeda.
--   3. Bisa juga dari aplikasi: menu PPM > Migrasi Lingkup, tombol
--      "Siapkan Kolom Tilik" (aman dijalankan berulang kali).
--
-- Aman dijalankan berulang kali? Tidak: ADD COLUMN gagal bila kolomnya sudah
-- ada. Bila `dtjwb_jawaban` sudah ada, lewati pernyataan ini.
-- ============================================================================

ALTER TABLE `mutu_auditjawabdetail`
  ADD COLUMN `dtjwb_jawaban` text DEFAULT NULL AFTER `dtjwb_pertanyaan`;
