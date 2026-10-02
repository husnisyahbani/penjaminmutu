-- ============================================================================
-- SIJAMU - Periode Audit Mutu Internal
--
-- Tabel:
--   mutu_periode : daftar periode (tahun, tanggal mulai, tanggal berakhir,
--                  penanda periode aktif)
-- Kolom:
--   mutu_audit.periode_id : relasi audit ke periode
--
-- Cara pakai:
--   1. Import file ini melalui phpMyAdmin / menu SQL pada database aplikasi.
--   2. Bila prefix tabel bukan 'mutu_', sesuaikan nama tabel di bawah.
--   3. Selesai - periode dapat dikelola dari aplikasi:
--      menu Audit > Periode.
--
-- Catatan: aplikasi juga dapat membuat tabel & kolom ini sendiri dari tombol
-- "Buat Tabel Periode" pada halaman Audit > Periode (aman dijalankan
-- berulang kali).
--
-- Satu periode saja yang aktif; periode aktif dipakai sebagai filter bawaan
-- pada halaman Daftar Audit. Bila tidak ada periode aktif, Daftar Audit
-- menampilkan seluruh data.
-- ============================================================================

CREATE TABLE IF NOT EXISTS `mutu_periode` (
  `periode_id`      INT(11) NOT NULL AUTO_INCREMENT,
  `periode_tahun`   VARCHAR(20) NOT NULL,
  `periode_mulai`   DATE NOT NULL,
  `periode_selesai` DATE NOT NULL,
  `periode_aktif`   TINYINT(1) NOT NULL DEFAULT 0,
  `periode_create`  DATETIME NULL,
  PRIMARY KEY (`periode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Relasi audit ke periode. Lewati baris ini bila kolomnya sudah ada
-- (MySQL belum mendukung ADD COLUMN IF NOT EXISTS pada semua versi).
ALTER TABLE `mutu_audit` ADD COLUMN `periode_id` INT(11) NULL;

-- Contoh isi awal (opsional, silakan sesuaikan):
-- INSERT INTO `mutu_periode` (`periode_tahun`, `periode_mulai`, `periode_selesai`, `periode_aktif`, `periode_create`)
-- VALUES ('2026', '2026-01-01', '2026-12-31', 1, NOW());
