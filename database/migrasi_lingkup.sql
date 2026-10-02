-- ============================================================================
-- SIJAMU - Migrasi lingkup audit
--
-- Sebelumnya satu pertanyaan audit (mutu_detailform) hanya punya SATU kolom
-- teks lingkup. Sekarang satu pertanyaan boleh punya BANYAK butir lingkup
-- pada tabel baru `mutu_lingkup`, dan jawaban audit (mutu_auditjawab)
-- berelasi ke butir tersebut melalui kolom `lingkup_id`.
--
-- Cara pakai:
--   1. Import berkas ini melalui phpMyAdmin (membuat/menyesuaikan struktur).
--   2. Jalankan pemindahan data dari aplikasi: menu
--      PPM > /admin/migrasi  (tombol "Jalankan Migrasi").
--      Pemindahan isi kolom lama dilakukan aplikasi karena isinya HTML
--      (daftar <li>, paragraf) sehingga perlu di-parse, bukan sekadar SQL.
--   3. Setelah hasilnya diperiksa, hapus kolom lama lewat tombol
--      "Hapus Kolom Lama" pada halaman yang sama (atau pernyataan di bawah).
--
-- Aman dijalankan berulang kali (IF NOT EXISTS / pengecekan sederhana).
-- Sesuaikan prefix `mutu_` bila berbeda.
-- ============================================================================

-- ---------------------------------------------------------------------------
-- 1. Tabel butir lingkup
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mutu_lingkup` (
  `lingkup_id`     int(11) NOT NULL AUTO_INCREMENT,
  `dtform_id`      int(11) NOT NULL,
  `lingkup_urut`   int(11) NOT NULL DEFAULT 0,
  `lingkup_isi`    text DEFAULT NULL,
  `lingkup_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `lingkup_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`lingkup_id`),
  KEY `lingkup_dtform_id` (`dtform_id`),
  CONSTRAINT `lingkup_dtform_id` FOREIGN KEY (`dtform_id`)
      REFERENCES `mutu_detailform` (`dtform_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------------
-- 2. Relasi jawaban audit ke butir lingkup
--    - jawaban per pertanyaan  -> lingkup_id NULL
--    - jawaban per butir       -> lingkup_id terisi
-- ---------------------------------------------------------------------------
ALTER TABLE `mutu_auditjawab`
  ADD COLUMN `lingkup_id` int(11) DEFAULT NULL AFTER `dtform_id`,
  ADD KEY `jawab_lingkup_id` (`lingkup_id`),
  ADD UNIQUE KEY `jawab_audit_lingkup` (`audit_id`, `lingkup_id`),
  ADD CONSTRAINT `jawab_lingkup_id` FOREIGN KEY (`lingkup_id`)
      REFERENCES `mutu_lingkup` (`lingkup_id`) ON DELETE SET NULL;

-- ---------------------------------------------------------------------------
-- 3. Penanda baris tilik lama yang sudah dipindahkan
--    (dipakai agar migrasi tidak memproses baris yang sama dua kali)
-- ---------------------------------------------------------------------------
ALTER TABLE `mutu_auditjawabdetail`
  ADD COLUMN `lingkup_id` int(11) DEFAULT NULL AFTER `jwb_id`;

-- ---------------------------------------------------------------------------
-- 4. Kolom lama tidak lagi wajib diisi (isinya sudah dipindah ke mutu_lingkup)
-- ---------------------------------------------------------------------------
ALTER TABLE `mutu_detailform`
  MODIFY COLUMN `dtform_lingkup` text DEFAULT NULL;

-- ---------------------------------------------------------------------------
-- 5. Setelah migrasi selesai dan hasilnya diperiksa, kolom lama dapat dibuang:
-- ---------------------------------------------------------------------------
-- ALTER TABLE `mutu_detailform` DROP COLUMN `dtform_lingkup`;
-- ALTER TABLE `mutu_auditjawabdetail` DROP COLUMN `lingkup_id`;  -- opsional
