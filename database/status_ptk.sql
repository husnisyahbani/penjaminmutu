-- ============================================================================
-- SIJAMU - Status Temuan PTK oleh Auditor (mutu_auditjawabdetail.dtjwb_status)
--
-- Menambahkan kolom status tindakan perbaikan pada tabel `mutu_auditjawabdetail`.
-- Kolom ini diisi oleh auditor pada halaman Monitoring PTK (OPEN / PROSES / CLOSED)
-- untuk memantau status penyelesaian temuan audit mutu.
--
-- Cara pakai:
--   1. Impor berkas ini melalui phpMyAdmin (atau tab SQL).
--   2. Sesuaikan prefix `mutu_` bila berbeda.
-- ============================================================================

ALTER TABLE `mutu_auditjawabdetail`
  ADD COLUMN `dtjwb_status` VARCHAR(20) DEFAULT NULL AFTER `dtjwb_koreksi`;
