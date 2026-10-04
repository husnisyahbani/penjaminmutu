-- ============================================================================
-- SIJAMU - Kelebihan & Peluang untuk peningkatan per butir lingkup
--
-- Pada halaman Auditor -> Daftar Audit -> (detail), di bawah jawaban auditee
-- pada tiap butir lingkup, auditor dapat mengisi:
--   1. Kelebihan
--   2. Peluang untuk peningkatan
--
-- Keduanya tersimpan per AUDIT dan per BUTIR LINGKUP, sehingga tidak menempel
-- pada baris tilik (mutu_auditjawabdetail) dan tetap tersimpan walau butir
-- tilik belum dibuat.
--
-- Cara pakai:
--   1. Impor berkas ini lewat phpMyAdmin (tab SQL) atau mysql.
--   2. Sesuaikan prefix `mutu_` bila berbeda.
--   3. Tanpa tabel ini aplikasi tetap berjalan: bagian "Kelebihan & Peluang
--      untuk peningkatan" tidak ditampilkan dan halaman memberi keterangan
--      bahwa fitur belum disiapkan.
--
-- Aman dijalankan berulang kali? Ya (CREATE TABLE IF NOT EXISTS).
-- ============================================================================

CREATE TABLE IF NOT EXISTS `mutu_lingkup_nilai` (
  `nilai_id` int(11) NOT NULL AUTO_INCREMENT,
  `audit_id` int(11) NOT NULL COMMENT 'mutu_audit.audit_id',
  `lingkup_id` int(11) NOT NULL COMMENT 'mutu_lingkup.lingkup_id',
  `kelebihan` text DEFAULT NULL,
  `peluang` text DEFAULT NULL COMMENT 'peluang untuk peningkatan',
  `users_id` int(11) DEFAULT NULL COMMENT 'auditor terakhir yang menyimpan',
  `nilai_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `nilai_update` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`nilai_id`),
  UNIQUE KEY `nilai_unik` (`audit_id`, `lingkup_id`),
  KEY `nilai_audit` (`audit_id`),
  KEY `nilai_lingkup` (`lingkup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Catatan auditor per butir lingkup';
