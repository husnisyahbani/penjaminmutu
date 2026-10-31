-- ============================================================================
-- SIJAMU - Lampiran jawaban per lingkup (butir) audit
--
-- Pada halaman detail audit auditee, tiap lingkup wajib dijawab dan boleh
-- dilengkapi lampiran (berkas bukti). Satu lingkup dapat memiliki lebih dari
-- satu lampiran, sehingga berkas disimpan pada tabel tersendiri.
--
-- Cara pakai:
--   1. Pastikan `database/migrasi_lingkup.sql` sudah diimpor (tabel mutu_lingkup).
--   2. Impor berkas ini melalui phpMyAdmin (atau tab SQL).
--   3. Sesuaikan prefix `mutu_` bila berbeda.
--
-- Aman dijalankan berulang kali? Ya: memakai CREATE TABLE IF NOT EXISTS.
--
-- Berkas fisiknya disimpan di folder `filedata/lampiran/` (dibuat otomatis
-- oleh aplikasi bila belum ada). Kolom `lampiran_nama` menyimpan nama berkas
-- di server (acak), `lampiran_asli` menyimpan nama asli dari pengunggah.
-- ============================================================================

CREATE TABLE IF NOT EXISTS `mutu_lampiran` (
  `lampiran_id`     int(11) NOT NULL AUTO_INCREMENT,
  `audit_id`        int(11) NOT NULL,
  `jwb_id`          int(11) DEFAULT NULL,
  `dtjwb_id`        int(11) DEFAULT NULL,
  `lingkup_id`      int(11) DEFAULT NULL,
  `users_id`        int(11) DEFAULT NULL,
  `lampiran_nama`   varchar(255) NOT NULL,
  `lampiran_asli`   varchar(255) NOT NULL,
  `lampiran_tipe`   varchar(100) DEFAULT NULL,
  `lampiran_ukuran` int(11) NOT NULL DEFAULT 0,
  `lampiran_create` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`lampiran_id`),
  KEY `lampiran_audit_id` (`audit_id`),
  KEY `lampiran_jwb_id` (`jwb_id`),
  KEY `lampiran_dtjwb_id` (`dtjwb_id`),
  KEY `lampiran_lingkup_id` (`lingkup_id`),
  CONSTRAINT `lampiran_audit_id` FOREIGN KEY (`audit_id`)
      REFERENCES `mutu_audit` (`audit_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel versi lama (sebelum ada kolom dtjwb_id) dapat disesuaikan dengan:
--   ALTER TABLE `mutu_lampiran`
--     ADD COLUMN `jwb_id` int(11) DEFAULT NULL AFTER `audit_id`,
--     ADD COLUMN `dtjwb_id` int(11) DEFAULT NULL AFTER `jwb_id`,
--     ADD KEY `lampiran_jwb_id` (`jwb_id`),
--     ADD KEY `lampiran_dtjwb_id` (`dtjwb_id`),
--     MODIFY `lingkup_id` int(11) DEFAULT NULL;
-- atau lewat tombol "Siapkan Kolom Tilik" pada menu PPM > Migrasi Lingkup.

-- Catatan:
-- - ON DELETE CASCADE: lampiran ikut terhapus bila audit atau lingkupnya
--   dihapus, sehingga tidak ada berkas yatim di database.
-- - Menghapus baris di tabel ini tidak menghapus berkas fisik; aplikasi
--   menghapus berkasnya lebih dahulu (lihat LampiranModel::hapus()).
-- - Lampiran menempel pada JAWABAN BUTIR LINGKUP (`jwb_id` -> mutu_auditjawab
--   baris yang `lingkup_id`-nya terisi, dengan `lingkup_id` ikut tercatat).
--   Auditee menjawab tiap butir lingkup (mutu_lingkup) pada halaman
--   Auditee -> Daftar Audit -> Detail, bukan butir tilik - butir tilik
--   (mutu_auditjawabdetail) hanya diisi auditor.
--   Lampiran lama yang menempel pada pertanyaan (`jwb_id` baris pertanyaan)
--   tetap terbaca dan hanya ditampilkan, tidak bisa ditambah lagi.
-- - Jawaban auditee disimpan pada `mutu_auditjawab`.`jwb_jawaban`.

-- ---------------------------------------------------------------------------
-- Bila ingin membatalkan:
-- ---------------------------------------------------------------------------
-- DROP TABLE IF EXISTS `mutu_lampiran`;
