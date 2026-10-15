-- =====================================================================
-- Tim auditor pada satu audit (menu PPM: Audit > Daftar Audit > Tambah)
--
-- Satu audit dapat dikerjakan lebih dari satu auditor: satu orang
-- menjadi KETUA TIM dan sisanya ANGGOTA. Tim dipilih pada formulir
-- "Tambah" di halaman Daftar Audit (admin).
--
-- Cara pakai: jalankan berkas ini di phpMyAdmin (tab SQL) atau lewat
-- mysql. Aman dijalankan berulang kali (CREATE TABLE IF NOT EXISTS).
-- Sesuaikan awalan `mutu_` bila basis data memakai awalan lain.
-- =====================================================================

CREATE TABLE IF NOT EXISTS `mutu_auditteams` (
  `tim_id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `audit_id`    INT(11)          NOT NULL COMMENT 'mutu_audit.audit_id',
  `users_id`    INT(11)          NOT NULL COMMENT 'users.users_id (role AUDITOR)',
  `tim_peran`   VARCHAR(10)      NOT NULL DEFAULT 'ANGGOTA' COMMENT 'KETUA atau ANGGOTA',
  `tim_urut`    INT(11)          NOT NULL DEFAULT 0 COMMENT 'ketua = 0, anggota 1..n',
  `tim_create`  DATETIME             NULL,
  PRIMARY KEY (`tim_id`),
  UNIQUE KEY `tim_unik` (`audit_id`, `users_id`),
  KEY `tim_audit` (`audit_id`),
  KEY `tim_pengguna` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tim auditor (ketua + anggota) per audit';

-- Catatan
-- ---------------------------------------------------------------------
-- 1. Kolom mutu_audit.auditor_id TETAP dipakai: selalu berisi users_id
--    ketua tim, sehingga fitur lama (ekspor, saringan, laporan) tidak
--    perlu diubah.
-- 2. Anggota tim ikut melihat dan menilai audit tersebut pada laman
--    Daftar Audit, detail audit, Daftar Tilik, dan PTK milik auditor.
-- 3. Tanpa tabel ini aplikasi tetap berjalan: audit hanya punya satu
--    auditor (mutu_audit.auditor_id) seperti sebelumnya.
-- 4. Menghapus audit otomatis menghapus baris timnya (TimauditModel).
-- 5. Bila tidak diperlukan lagi: DROP TABLE `mutu_auditteams`;
