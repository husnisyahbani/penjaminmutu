-- ============================================================================
-- SIJAMU - Tabel sesi login (session driver "database" milik CodeIgniter)
--
-- Aplikasi memakai:
--   $config['sess_driver']    = 'database';
--   $config['sess_save_path'] = 'ci_sessions';
--
-- Query builder CodeIgniter menambahkan prefix `mutu_` dari
-- application/config/database.php, sehingga tabel yang dipakai aplikasi
-- adalah `mutu_ci_sessions` (bukan `ci_sessions`).
--
-- Cara pakai:
--   1. Impor berkas ini melalui phpMyAdmin (atau tab SQL).
--   2. Bila prefix tabel di database.php bukan `mutu_`, sesuaikan namanya
--      (contoh tanpa prefix ada di bagian bawah berkas ini).
--   3. Tidak ada data awal yang perlu diisi: baris sesi dibuat otomatis
--      saat pengguna login.
--
-- Aman dijalankan berulang kali (CREATE TABLE IF NOT EXISTS).
-- ============================================================================

CREATE TABLE IF NOT EXISTS `mutu_ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Catatan:
-- - `data` bertipe binary (blob) mengikuti bawaan CodeIgniter. Bila isi sesi
--   perlu lebih dari 64 KB, ganti `blob` menjadi `mediumblob`.
-- - Sesi kedaluwarsa (bawaan: 7200 detik) dibersihkan otomatis oleh aplikasi
--   lewat proses garbage collection.
-- - Untuk memaksa semua pengguna login ulang, kosongkan tabel:
--   TRUNCATE TABLE `mutu_ci_sessions`;

-- ---------------------------------------------------------------------------
-- Bila prefix tabel di application/config/database.php kosong ('') atau
-- berbeda, pakai bentuk di bawah ini dengan nama tabel yang sesuai:
-- ---------------------------------------------------------------------------
-- CREATE TABLE IF NOT EXISTS `ci_sessions` (
--   `id` varchar(128) NOT NULL,
--   `ip_address` varchar(45) NOT NULL,
--   `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
--   `data` blob NOT NULL,
--   PRIMARY KEY (`id`),
--   KEY `ci_sessions_timestamp` (`timestamp`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
