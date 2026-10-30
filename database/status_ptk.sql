-- ============================================================================
-- SIJAMU - Status Temuan PTK oleh Auditor (mutu_auditjawabdetail.dtjwb_status)
--
-- Menambahkan kolom status tindakan perbaikan pada tabel `mutu_auditjawabdetail`.
-- Kolom ini diisi oleh auditor pada halaman Monitoring PTK dengan TEKS BEBAS
-- (mis. "Menunggu bukti", "Proses", "Selesai") untuk memantau status
-- penyelesaian temuan audit mutu - seperti auditee menulis rencana koreksi.
--
-- Cara pakai:
--   1. Impor berkas ini melalui phpMyAdmin (atau tab SQL).
--   2. Sesuaikan prefix `mutu_` bila berbeda.
-- ============================================================================

ALTER TABLE `mutu_auditjawabdetail`
  ADD COLUMN `dtjwb_status` TEXT NULL
  COMMENT 'Status tindak lanjut PTK, teks bebas yang diisi auditor'
  AFTER `dtjwb_koreksi`;

-- ---------------------------------------------------------------------------
-- Catatan pembaruan
--
-- Status PTK sekarang berupa TEKS BEBAS yang diketik auditor pada laman
-- /auditor/ptk (bukan lagi pilihan tetap OPEN / PROSES / CLOSED), seperti
-- auditee menulis rencana koreksi pada halamannya. Karena itu kolomnya
-- dibuat TEXT.
--
-- Bila kolom ini sudah telanjur dibuat sebagai VARCHAR(20), perbesar
-- menjadi TEXT dengan pernyataan berikut (aman dijalankan berulang kali,
-- dan data yang sudah tersimpan tidak hilang):
-- ---------------------------------------------------------------------------

-- ALTER TABLE `mutu_auditjawabdetail` MODIFY COLUMN `dtjwb_status` TEXT NULL;

-- Perintah yang sama juga tersedia pada menu PPM > Migrasi
-- ("Siapkan Kolom Status PTK"), jadi tidak perlu membuka phpMyAdmin.
