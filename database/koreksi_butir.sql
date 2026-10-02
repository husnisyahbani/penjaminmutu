-- ============================================================================
-- SIJAMU - Rencana koreksi per butir tilik (mutu_auditjawabdetail)
--
-- Halaman PTK dan halaman delik auditee menampilkan satu baris per butir
-- tilik hasil penilaian auditor (tabel `mutu_auditjawabdetail`). Rencana
-- koreksi yang ditulis auditee disimpan:
--
--   1. pada kolom `dtjwb_koreksi` (berkas ini) -> satu teks untuk tiap butir;
--   2. bila kolomnya belum ada, aplikasi memakai kolom lama
--      `mutu_auditjawab.jwb_koreksi` -> satu teks untuk satu pertanyaan
--      (semua butir pada pertanyaan itu menampilkan teks yang sama).
--
-- Karena itu berkas ini dianjurkan diimpor supaya rencana koreksi tersimpan
-- per butir.
--
-- Cara pakai:
--   1. Impor berkas ini melalui phpMyAdmin (atau tab SQL).
--   2. Sesuaikan prefix `mutu_` bila berbeda.
--   3. Bisa juga dari aplikasi: menu PPM > Migrasi Lingkup, tombol
--      "Siapkan Kolom Koreksi Butir" (aman dijalankan berulang kali).
--
-- Aman dijalankan berulang kali? Tidak: ADD COLUMN akan gagal bila kolomnya
-- sudah ada. Bila kolom `dtjwb_koreksi` sudah ada, lewati pernyataan ini.
-- ============================================================================

ALTER TABLE `mutu_auditjawabdetail`
  ADD COLUMN `dtjwb_koreksi` text DEFAULT NULL AFTER `dtjwb_catatan`;
