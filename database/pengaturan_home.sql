-- ============================================================================
-- SIJAMU - Pengaturan Tampilan Halaman Depan (Homepage)
--
-- Tabel:
--   mutu_home_setting : pengaturan nilai tunggal (teks, gambar, warna, tombol)
--   mutu_home_item    : konten berulang (kartu akses, galeri, misi, tupoksi, sasaran)
--
-- Cara pakai:
--   1. Import file ini melalui phpMyAdmin / menu SQL pada database aplikasi.
--   2. Bila prefix tabel bukan 'mutu_', sesuaikan nama tabel di bawah.
--   3. Pengaturan dapat diubah dari aplikasi: Website > Pengaturan Home.
--
-- Import ulang aman untuk tabel pengaturan (INSERT IGNORE + unique key).
-- Untuk konten item, jalankan bagian INSERT di bawah satu kali saja; bila
-- tabel mutu_home_item sudah terisi, lewati bagian tersebut.
--
-- Catatan: bila tabel belum dibuat, halaman depan tetap tampil memakai
-- konten bawaan. Tabel juga dapat dibuat dari tombol 'Buat Tabel Pengaturan'
-- pada menu Website > Pengaturan Home.
-- ============================================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `mutu_home_setting` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_kunci` varchar(100) NOT NULL,
  `setting_grup` varchar(50) NOT NULL DEFAULT 'umum',
  `setting_label` varchar(150) DEFAULT NULL,
  `setting_tipe` varchar(20) NOT NULL DEFAULT 'text',
  `setting_nilai` text,
  `setting_urutan` int(11) NOT NULL DEFAULT 0,
  `setting_update` datetime DEFAULT NULL,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `setting_kunci` (`setting_kunci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mutu_home_item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_grup` varchar(50) NOT NULL,
  `item_judul` varchar(200) DEFAULT NULL,
  `item_isi` text,
  `item_gambar` varchar(200) DEFAULT NULL,
  `item_ikon` varchar(60) DEFAULT NULL,
  `item_link` varchar(200) DEFAULT NULL,
  `item_label_link` varchar(100) DEFAULT NULL,
  `item_warna` varchar(30) DEFAULT NULL,
  `item_urutan` int(11) NOT NULL DEFAULT 0,
  `item_status` tinyint(1) NOT NULL DEFAULT 1,
  `item_create` datetime DEFAULT NULL,
  `item_update` datetime DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `item_grup` (`item_grup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `mutu_home_setting`
  (`setting_kunci`, `setting_grup`, `setting_label`, `setting_tipe`, `setting_nilai`, `setting_urutan`, `setting_update`) VALUES
  ('site_nama', 'umum', 'Nama Sistem', 'text', 'SIJAMU', 1, NOW()),
  ('site_subjudul', 'umum', 'Nama Panjang Sistem', 'text', 'Sistem Informasi Penjaminan Mutu', 2, NOW()),
  ('site_institusi', 'umum', 'Nama Institusi', 'text', 'STIK Siti Khadijah', 3, NOW()),
  ('site_logo', 'umum', 'Logo', 'image', 'assets/assets/images/logostik.png', 4, NOW()),
  ('warna_primer', 'umum', 'Warna Utama', 'color', '#0f766e', 5, NOW()),
  ('warna_aksen', 'umum', 'Warna Aksen', 'color', '#f59e0b', 6, NOW()),
  ('hero_badge', 'hero', 'Label Kecil (badge)', 'text', 'Pusat Penjaminan Mutu', 7, NOW()),
  ('hero_judul', 'hero', 'Judul Utama', 'text', 'SIJAMU', 8, NOW()),
  ('hero_subjudul', 'hero', 'Sub Judul', 'text', 'Sistem Informasi Penjaminan Mutu', 9, NOW()),
  ('hero_deskripsi', 'hero', 'Deskripsi', 'textarea', 'Media informasi, monitoring, dan evaluasi mutu untuk mendukung pelaksanaan Sistem Penjaminan Mutu Internal STIK Siti Khadijah secara terintegrasi, transparan, dan berkelanjutan dalam mewujudkan pendidikan tinggi yang bermutu.', 10, NOW()),
  ('hero_gambar', 'hero', 'Ilustrasi Hero', 'image', 'assets/assets/images/sijamuhomepage.png', 11, NOW()),
  ('hero_cta_label', 'hero', 'Tombol Utama - Teks', 'text', 'Pelajari Lebih Lanjut', 12, NOW()),
  ('hero_cta_link', 'hero', 'Tombol Utama - Link', 'text', '#profil', 13, NOW()),
  ('hero_cta2_label', 'hero', 'Tombol Kedua - Teks', 'text', 'Masuk / Login', 14, NOW()),
  ('hero_cta2_link', 'hero', 'Tombol Kedua - Link', 'text', 'login', 15, NOW()),
  ('hero_catatan', 'hero', 'Catatan Kecil', 'text', 'Terintegrasi  •  Transparan  •  Berkelanjutan', 16, NOW()),
  ('akses_judul', 'akses', 'Judul Section', 'text', 'Masuk Sesuai Peran Anda', 17, NOW()),
  ('akses_deskripsi', 'akses', 'Deskripsi', 'textarea', 'Setiap pengguna memiliki hak akses yang berbeda. Silakan masuk menggunakan akun yang telah diberikan oleh Pusat Penjaminan Mutu.', 18, NOW()),
  ('profil_judul', 'profil', 'Judul Section', 'text', 'Profil Pusat Penjaminan Mutu', 19, NOW()),
  ('profil_ringkas', 'profil', 'Paragraf Pembuka', 'textarea', '<strong>Pusat Penjaminan Mutu (PPM) STIK Siti Khadijah</strong> merupakan unit struktural yang bertanggung jawab dalam merencanakan, melaksanakan, mengevaluasi, mengendalikan, dan meningkatkan mutu penyelenggaraan Tridharma Perguruan Tinggi serta tata kelola institusi.', 20, NOW()),
  ('profil_isi', 'profil', 'Paragraf Kedua', 'textarea', 'PPM berperan sebagai penggerak utama dalam implementasi <strong>Sistem Penjaminan Mutu Internal (SPMI)</strong> secara berkelanjutan. Dalam menjalankan fungsinya, PPM memastikan seluruh kegiatan akademik dan non-akademik berjalan sesuai standar yang ditetapkan, peraturan perundang-undangan, serta kebijakan nasional pendidikan tinggi. Selain itu, PPM menjadi pusat koordinasi pelaksanaan Audit Mutu Internal sebagai instrumen evaluasi dan peningkatan mutu institusi.', 21, NOW()),
  ('pengelola_judul', 'pengelola', 'Judul Section', 'text', 'Pengelola Pusat Penjaminan Mutu', 22, NOW()),
  ('pengelola_deskripsi', 'pengelola', 'Deskripsi', 'textarea', 'Susunan personel Pusat Penjaminan Mutu STIK Siti Khadijah yang bertugas menjalankan siklus penjaminan mutu internal.', 23, NOW()),
  ('pengelola_gambar', 'pengelola', 'Gambar Struktur Pengelola', 'image', 'assets/assets/images/tim.png', 24, NOW()),
  ('struktur_judul', 'struktur', 'Judul Section', 'text', 'Struktur Organisasi', 25, NOW()),
  ('struktur_deskripsi', 'struktur', 'Deskripsi', 'textarea', 'Bagan organisasi Pusat Penjaminan Mutu beserta garis koordinasi dengan unit kerja di lingkungan institusi.', 26, NOW()),
  ('struktur_gambar', 'struktur', 'Gambar Struktur Organisasi', 'image', 'assets/assets/images/strukturorganisasi_fix.png', 27, NOW()),
  ('visi_judul', 'visi', 'Judul Section', 'text', 'Visi & Misi', 28, NOW()),
  ('visi_isi', 'visi', 'Isi Visi', 'textarea', 'Menjadi pusat penjaminan mutu yang unggul dalam mengembangkan dan mengawal budaya mutu berkelanjutan guna mendukung terwujudnya <strong>STIK Siti Khadijah</strong> yang berkualitas dan berdaya saing.', 29, NOW()),
  ('tupoksi_judul', 'tupoksi', 'Judul Section', 'text', 'Tugas Pokok dan Fungsi', 30, NOW()),
  ('sasaran_judul', 'sasaran', 'Judul Section', 'text', 'Sasaran Mutu', 31, NOW()),
  ('sasaran_umum_judul', 'sasaran', 'Judul Sasaran Umum', 'text', 'Sasaran Mutu - Umum', 32, NOW()),
  ('sasaran_umum_isi', 'sasaran', 'Isi Sasaran Umum', 'textarea', 'Mewujudkan terlaksananya Sistem Penjaminan Mutu Internal (SPMI) secara konsisten dan berkelanjutan guna menjamin dan meningkatkan mutu penyelenggaraan Tridharma Perguruan Tinggi serta tata kelola institusi di lingkungan <strong>STIK Siti Khadijah</strong>.', 33, NOW()),
  ('sasaran_khusus_judul', 'sasaran', 'Judul Sasaran Khusus', 'text', 'Sasaran Mutu - Khusus', 34, NOW()),
  ('sk_judul', 'dokumen', 'Judul Section', 'text', 'Surat Keputusan', 35, NOW()),
  ('sk_deskripsi', 'dokumen', 'Deskripsi', 'textarea', 'Dokumen surat keputusan yang berkaitan dengan penyelenggaraan penjaminan mutu.', 36, NOW()),
  ('berita_judul', 'berita', 'Judul Section', 'text', 'Berita Terbaru', 37, NOW()),
  ('berita_deskripsi', 'berita', 'Deskripsi', 'textarea', 'Kabar terbaru seputar kegiatan penjaminan mutu.', 38, NOW()),
  ('pengumuman_judul', 'pengumuman', 'Judul Section', 'text', 'Pengumuman', 39, NOW()),
  ('pengumuman_deskripsi', 'pengumuman', 'Deskripsi', 'textarea', 'Informasi resmi dari Pusat Penjaminan Mutu.', 40, NOW()),
  ('kontak_judul', 'kontak', 'Judul Section', 'text', 'Hubungi Kami', 41, NOW()),
  ('kontak_alamat', 'kontak', 'Alamat', 'textarea', 'Jl. Demang Lebar Daun, Kelurahan Lorok Pakjo, Kecamatan Ilir Barat I, Kota Palembang, Sumatera Selatan 30137', 42, NOW()),
  ('kontak_telepon', 'kontak', 'Telepon', 'text', '(0711) 315010', 43, NOW()),
  ('kontak_email', 'kontak', 'Email', 'text', 'spmi@stik-sitikhadijah.ac.id', 44, NOW()),
  ('kontak_website', 'kontak', 'Website', 'text', 'https://stik-sitikhadijah.ac.id', 45, NOW()),
  ('kontak_maps', 'kontak', 'Embed Peta (opsional)', 'textarea', '', 46, NOW()),
  ('footer_teks', 'kontak', 'Teks Footer', 'text', 'Dikembangkan oleh STIK Siti Khadijah', 47, NOW()),
  ('footer_kredit', 'kontak', 'Teks Kredit', 'text', 'Pusat Penjaminan Mutu © 2026', 48, NOW()),
  ('section_statistik', 'section', 'Statistik Singkat', 'toggle', '1', 49, NOW()),
  ('section_kartu', 'section', 'Kartu Akses / Login', 'toggle', '1', 50, NOW()),
  ('section_profil', 'section', 'Profil', 'toggle', '1', 51, NOW()),
  ('section_visi', 'section', 'Visi & Misi', 'toggle', '1', 52, NOW()),
  ('section_tupoksi', 'section', 'Tugas Pokok & Fungsi', 'toggle', '1', 53, NOW()),
  ('section_sasaran', 'section', 'Sasaran Mutu', 'toggle', '1', 54, NOW()),
  ('section_pengelola', 'section', 'Pengelola', 'toggle', '1', 55, NOW()),
  ('section_struktur', 'section', 'Struktur Organisasi', 'toggle', '1', 56, NOW()),
  ('section_sk', 'section', 'Surat Keputusan', 'toggle', '1', 57, NOW()),
  ('section_berita', 'section', 'Berita', 'toggle', '1', 58, NOW()),
  ('section_pengumuman', 'section', 'Pengumuman', 'toggle', '1', 59, NOW()),
  ('section_kontak', 'section', 'Kontak', 'toggle', '1', 60, NOW());

INSERT INTO `mutu_home_item`
  (`item_grup`, `item_judul`, `item_isi`, `item_gambar`, `item_ikon`, `item_link`, `item_label_link`, `item_warna`, `item_urutan`, `item_status`, `item_create`, `item_update`) VALUES
  ('kartu', 'PPM', 'Pusat Penjaminan Mutu - kelola dokumen, standar, dan siklus penjaminan mutu.', '', 'bi-shield-check', 'login', 'Login sebagai PPM', '#0f766e', 1, 1, NOW(), NOW()),
  ('kartu', 'Auditor', 'Anggota PPM yang melaksanakan audit mutu internal terhadap unit kerja.', '', 'bi-clipboard-check', 'login', 'Login sebagai Auditor', '#f59e0b', 2, 1, NOW(), NOW()),
  ('kartu', 'Auditee', 'Unit kerja atau program studi yang menjadi objek audit mutu internal.', '', 'bi-people', 'login', 'Login sebagai Auditee', '#dc2626', 3, 1, NOW(), NOW()),
  ('misi', '', 'Mengembangkan dan mengimplementasikan Sistem Penjaminan Mutu Internal (SPMI) secara konsisten dan berkelanjutan sesuai standar nasional pendidikan tinggi.', '', '', '', '', '', 1, 1, NOW(), NOW()),
  ('misi', '', 'Menyusun, menetapkan, dan mengembangkan standar mutu akademik dan non-akademik sebagai pedoman penyelenggaraan kegiatan institusi.', '', '', '', '', '', 2, 1, NOW(), NOW()),
  ('misi', '', 'Melaksanakan monitoring, evaluasi, dan Audit Mutu Internal (AMI) secara berkala terhadap program studi dan unit kerja.', '', '', '', '', '', 3, 1, NOW(), NOW()),
  ('misi', '', 'Mendorong terlaksananya siklus PPEPP (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan) sebagai budaya mutu di seluruh unit.', '', '', '', '', '', 4, 1, NOW(), NOW()),
  ('misi', '', 'Meningkatkan kompetensi sumber daya manusia dalam bidang penjaminan mutu melalui pelatihan, pendampingan, dan penyegaran auditor.', '', '', '', '', '', 5, 1, NOW(), NOW()),
  ('misi', '', 'Mendukung peningkatan mutu Tridharma Perguruan Tinggi dan tata kelola institusi dalam rangka pencapaian akreditasi yang unggul.', '', '', '', '', '', 6, 1, NOW(), NOW()),
  ('tupoksi', '', 'Merencanakan, melaksanakan, dan mengembangkan penyelenggaraan pendidikan berdasarkan peraturan dan pedoman sistem penjaminan mutu.', '', '', '', '', '', 1, 1, NOW(), NOW()),
  ('tupoksi', '', 'Menyusun perangkat penjamin mutu.', '', '', '', '', '', 2, 1, NOW(), NOW()),
  ('tupoksi', '', 'Memonitor dan mengevaluasi pelaksanaan penyelenggaraan Tridharma Perguruan Tinggi.', '', '', '', '', '', 3, 1, NOW(), NOW()),
  ('tupoksi', '', 'Melaksanakan dan mengembangkan Audit Mutu Internal (AMI).', '', '', '', '', '', 4, 1, NOW(), NOW()),
  ('tupoksi', '', 'Menyiapkan auditor Audit Mutu Internal.', '', '', '', '', '', 5, 1, NOW(), NOW()),
  ('tupoksi', '', 'Menyelenggarakan rapat tinjauan manajemen.', '', '', '', '', '', 6, 1, NOW(), NOW()),
  ('tupoksi', '', 'Memonitor dan mengevaluasi pelaksanaan hasil rapat tinjauan manajemen.', '', '', '', '', '', 7, 1, NOW(), NOW()),
  ('tupoksi', '', 'Mendampingi proses akreditasi dan reakreditasi institusi, program studi, dan unit pelayanan pendidikan lainnya.', '', '', '', '', '', 8, 1, NOW(), NOW()),
  ('tupoksi', '', 'Mengembangkan sistem informasi pendukung Sistem Penjaminan Mutu Internal (SPMI).', '', '', '', '', '', 9, 1, NOW(), NOW()),
  ('sasaran', '', 'Terlaksananya penyelenggaraan pendidikan sesuai dengan kebijakan dan standar SPMI.', '', '', '', '', '', 1, 1, NOW(), NOW()),
  ('sasaran', '', 'Tersusunnya perangkat penjaminan mutu yang lengkap dan mutakhir.', '', '', '', '', '', 2, 1, NOW(), NOW()),
  ('sasaran', '', 'Terlaksananya monitoring dan evaluasi Tridharma Perguruan Tinggi secara berkala.', '', '', '', '', '', 3, 1, NOW(), NOW()),
  ('sasaran', '', 'Terlaksananya Audit Mutu Internal (AMI) secara sistematis dan berkelanjutan.', '', '', '', '', '', 4, 1, NOW(), NOW()),
  ('sasaran', '', 'Tersedianya auditor mutu internal yang kompeten.', '', '', '', '', '', 5, 1, NOW(), NOW()),
  ('sasaran', '', 'Terselenggaranya Rapat Tinjauan Manajemen (RTM) secara rutin.', '', '', '', '', '', 6, 1, NOW(), NOW()),
  ('sasaran', '', 'Terlaksananya tindak lanjut hasil Rapat Tinjauan Manajemen.', '', '', '', '', '', 7, 1, NOW(), NOW()),
  ('sasaran', '', 'Terlaksananya pendampingan akreditasi dan reakreditasi secara optimal.', '', '', '', '', '', 8, 1, NOW(), NOW()),
  ('sasaran', '', 'Berkembangnya sistem informasi pendukung SPMI yang efektif.', '', '', '', '', '', 9, 1, NOW(), NOW()),
  ('slider', 'Personel Pusat Penjaminan Mutu', '', 'assets/assets/images/personil.jpg', '', '', '', '', 1, 1, NOW(), NOW()),
  ('slider', 'Kegiatan Audit Mutu Internal', '', 'assets/assets/images/personil2.jpg', '', '', '', '', 2, 1, NOW(), NOW());
