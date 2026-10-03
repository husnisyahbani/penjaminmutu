-- ============================================================================
-- SIJAMU - Hapus kolom penilaian lama pada `mutu_auditjawab`
--
-- Kolom-kolom ini tidak dipakai lagi karena penilaian auditor dan rencana
-- koreksi auditee kini tersimpan per butir tilik pada `mutu_auditjawabdetail`
-- (`dtjwb_hasil`, `dtjwb_temuan`, `dtjwb_catatan`, `dtjwb_koreksi`), sedangkan
-- `mutu_auditjawab` hanya menyimpan jawaban auditee (`jwb_jawaban`) dan tujuan
-- pertanyaan (`jwb_tujuan`).
--
-- Yang dihapus:
--   jwb_pertanyaan  -> teks pertanyaan; kini berasal dari mutu_detailform /
--                      mutu_lingkup.
--   jwb_referensi   -> referensi; kini pada mutu_auditjawabdetail.dtjwb_referensi.
--   jwb_temuan      -> hasil penilaian; kini mutu_auditjawabdetail.dtjwb_temuan.
--   jwb_hasil       -> uraian hasil; kini mutu_auditjawabdetail.dtjwb_hasil.
--   jwb_catatan     -> catatan; kini mutu_auditjawabdetail.dtjwb_catatan.
--   jwb_koreksi     -> rencana koreksi; kini mutu_auditjawabdetail.dtjwb_koreksi
--                      (impor database/koreksi_butir.sql bila belum ada).
--
-- Cara pakai:
--   1. Impor berkas ini lewat phpMyAdmin (tab SQL) atau mysql.
--   2. Sesuaikan prefix `mutu_` bila berbeda.
--   3. Jalankan SETELAH kode aplikasi diperbarui (kolom sudah tidak dibaca
--      lagi) dan sebaiknya setelah membuat cadangan basis data.
--
-- Aman dijalankan berulang kali? Tidak: DROP COLUMN akan gagal bila kolomnya
-- sudah terlanjur dihapus. Itu tidak berbahaya - abaikan pesan galatnya.
-- ============================================================================

ALTER TABLE `mutu_auditjawab`
  DROP `jwb_pertanyaan`,
  DROP `jwb_referensi`,
  DROP `jwb_temuan`,
  DROP `jwb_hasil`,
  DROP `jwb_catatan`,
  DROP `jwb_koreksi`;
