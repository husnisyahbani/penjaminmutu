-- ============================================================================
-- SIJAMU - Relasi formulir audit ke periode
--
-- Formulir audit (mutu_formulir) kini terikat pada satu periode, sehingga
-- daftar formulir dapat disaring per periode dengan bawaan periode aktif.
--
-- Cara pakai:
--   1. Impor berkas ini melalui phpMyAdmin (atau tab SQL).
--   2. Sesuaikan prefix `mutu_` bila berbeda.
--   3. Tabel `mutu_periode` harus sudah ada (lihat database/periode_audit.sql);
--      relasi ini opsional — formulir tanpa periode tetap boleh ada
--      (mis. formulir lama) dan tidak hilang dari daftar.
--
-- Aman dijalankan berulang kali? Tidak: ALTER TABLE ADD COLUMN akan gagal bila
-- kolomnya sudah ada. Bila kolom `periode_id` sudah ada, lewati pernyataan ini.
-- ============================================================================

ALTER TABLE `mutu_formulir`
  ADD COLUMN `periode_id` int(11) DEFAULT NULL AFTER `users_id`,
  ADD KEY `formulir_periode_id` (`periode_id`),
  ADD CONSTRAINT `formulir_periode_id` FOREIGN KEY (`periode_id`)
      REFERENCES `mutu_periode` (`periode_id`) ON DELETE SET NULL;

-- Catatan:
-- - ON DELETE SET NULL: formulir tidak ikut terhapus saat periodenya dihapus;
--   hanya relasinya yang dilepas.
-- - Formulir yang belum berperiode (periode_id NULL) tetap tampil pada daftar
--   maupun pada pilihan saat menyaring/membuat audit.

-- ---------------------------------------------------------------------------
-- Opsional: mengisi periode formulir lama dengan periode aktif saat ini.
-- ---------------------------------------------------------------------------
-- UPDATE `mutu_formulir` SET `periode_id` =
--   (SELECT `periode_id` FROM `mutu_periode` WHERE `periode_aktif` = 1 LIMIT 1)
--   WHERE `periode_id` IS NULL;

-- ---------------------------------------------------------------------------
-- Bila ingin membatalkan relasi ini:
-- ---------------------------------------------------------------------------
-- ALTER TABLE `mutu_formulir` DROP FOREIGN KEY `formulir_periode_id`;
-- ALTER TABLE `mutu_formulir` DROP KEY `formulir_periode_id`;
-- ALTER TABLE `mutu_formulir` DROP COLUMN `periode_id`;
