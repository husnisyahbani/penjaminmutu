-- ============================================================================
-- SIJAMU - Catatan Pengembalian Audit ke Auditee (mutu_audit.audit_catatan)
--
-- Saat auditor mengembalikan formulir evaluasi yang sedang diproses kepada
-- auditee (status kembali ke DRAFT), auditor dapat menyertakan catatan atau
-- instruksi perbaikan melalui form modal. Catatan tersebut disimpan pada kolom
-- `audit_catatan` di tabel `mutu_audit`.
--
-- Cara pakai:
--   1. Impor berkas ini melalui phpMyAdmin (atau tab SQL).
--   2. Sesuaikan prefix `mutu_` bila berbeda.
-- ============================================================================

ALTER TABLE `mutu_audit`
  ADD COLUMN `audit_catatan` text DEFAULT NULL AFTER `audit_status`;
