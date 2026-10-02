-- ============================================================================
-- SIJAMU - Urutan topik (pertanyaan) formulir audit
--
-- Halaman kelola formulir kini bergaya kursus: topik (pertanyaan) dan activity
-- (butir lingkup) dapat digeser naik/turun. Urutan activity memakai kolom
-- `mutu_lingkup`.`lingkup_urut` yang sudah ada; urutan topik memerlukan kolom
-- baru berikut.
--
-- Cara pakai:
--   1. Impor berkas ini melalui phpMyAdmin (atau tab SQL).
--   2. Sesuaikan prefix `mutu_` bila berbeda.
--   3. Bisa juga dari aplikasi: menu Audit > Formulir Audit > pilih formulir,
--      lalu klik tombol "Aktifkan Urutan Topik" (aman dijalankan berulang kali).
--
-- Isi awal `dtform_urut` mengikuti urutan `dtform_id`, sehingga tampilan tidak
-- berubah sebelum ada topik yang digeser.
--
-- Aman dijalankan berulang kali? Tidak: `ADD COLUMN` gagal bila kolomnya sudah
-- ada. Bila `dtform_urut` sudah ada, lewati berkas ini.
-- ============================================================================

ALTER TABLE `mutu_detailform`
  ADD COLUMN `dtform_urut` int(11) NOT NULL DEFAULT 0 AFTER `dtform_lingkup`;

-- Isi urutan awal (1, 2, 3, ...) per formulir mengikuti urutan id.
CREATE TEMPORARY TABLE `tmp_urut_dtform` AS
  SELECT a.`dtform_id`, a.`form_id`,
         (SELECT COUNT(*) FROM `mutu_detailform` b
           WHERE b.`form_id` = a.`form_id` AND b.`dtform_id` < a.`dtform_id`) + 1 AS `urut`
    FROM `mutu_detailform` a;

UPDATE `mutu_detailform` d
  JOIN `tmp_urut_dtform` t ON t.`dtform_id` = d.`dtform_id`
   SET d.`dtform_urut` = t.`urut`;

DROP TEMPORARY TABLE `tmp_urut_dtform`;

-- Catatan:
-- - Kolom ini tidak wajib. Tanpa kolomnya aplikasi tetap jalan: urutan topik
--   memakai `dtform_id` dan tombol naik/turun topik dinonaktifkan.
-- - Halaman kelola formulir menampilkan tombol "Aktifkan Urutan Topik" selama
--   kolom ini belum ada.

-- ---------------------------------------------------------------------------
-- Bila ingin membatalkan:
-- ---------------------------------------------------------------------------
-- ALTER TABLE `mutu_detailform` DROP COLUMN `dtform_urut`;
