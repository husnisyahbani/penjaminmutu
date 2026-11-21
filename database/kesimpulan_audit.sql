-- ============================================================================
-- SIJAMU - Kesimpulan audit (Bagian VII Laporan Audit Mutu Internal)
--
-- Jawaban atas tiga pertanyaan kesimpulan pada Laporan AMI:
--   1. Sistem dokumen cukup lengkap dan terstruktur untuk mendukung
--      pelaksanaan Sistem Penjamin Mutu Internal. (Ya / Tidak / Lainnya)
--   2. Program studi/unit telah menjalankan Sistem Penjamin Mutu Internal
--      secara konsisten dan berkelanjutan. (Ya / Tidak / Lainnya)
--   3. Temuan pada periode audit ini adalah: (Mayor / Minor / Observasi)
--
-- Jawaban diisi lewat modal sebelum mengunduh Laporan AMI; bila audit ini
-- belum pernah diisi, modal ditampilkan terlebih dahulu. Jawabannya
-- tercatat di sini dan dituliskan pada berkas Excel Laporan AMI.
--
-- Cara pakai:
--   1. Impor berkas ini lewat phpMyAdmin (tab SQL) atau mysql.
--   2. Sesuaikan prefix `mutu_` bila berbeda.
--   3. Tabel ini wajib tersedia untuk menyimpan kesimpulan dan mengunduh laporan.
--
-- Aman dijalankan berulang kali? Ya (CREATE TABLE IF NOT EXISTS).
-- ============================================================================

CREATE TABLE IF NOT EXISTS `mutu_auditkesimpulan` (
  `kesimpulan_id` int(11) NOT NULL AUTO_INCREMENT,
  `audit_id` int(11) NOT NULL COMMENT 'mutu_audit.audit_id',
  `kesimpulan1` varchar(20) DEFAULT NULL COMMENT 'Ya / Tidak / Lainnya',
  `kesimpulan1_lainnya` varchar(255) DEFAULT NULL COMMENT 'Sebutan bila Lainnya',
  `kesimpulan2` varchar(20) DEFAULT NULL COMMENT 'Ya / Tidak / Lainnya',
  `kesimpulan2_lainnya` varchar(255) DEFAULT NULL COMMENT 'Sebutan bila Lainnya',
  `kesimpulan3` varchar(20) DEFAULT NULL COMMENT 'Mayor / Minor / Observasi',
  `users_id` int(11) DEFAULT NULL COMMENT 'pengguna terakhir yang menyimpan',
  `kesimpulan_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `kesimpulan_update` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`kesimpulan_id`),
  UNIQUE KEY `kesimpulan_audit` (`audit_id`),
  KEY `kesimpulan_audit_id` (`audit_id`),
  CONSTRAINT `kesimpulan_audit_id` FOREIGN KEY (`audit_id`)
      REFERENCES `mutu_audit` (`audit_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='Kesimpulan audit (Bagian VII Laporan AMI)';
