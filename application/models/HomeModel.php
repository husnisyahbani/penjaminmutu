<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * HomeModel
 *
 * Sumber data untuk HALAMAN DEPAN (homepage) sekaligus pengaturan tampilannya
 * di panel admin (menu Website > Pengaturan Home).
 *
 * Model ini dipakai oleh dua module:
 *  - umum/Homepage   -> membaca pengaturan + item untuk dirender di halaman depan
 *  - admin/Pengaturanhome -> CRUD pengaturan tampilan halaman depan
 *
 * Karena itu model diletakkan di application/models/ (bukan di dalam module)
 * agar dapat diload dari module manapun.
 *
 * Tabel:
 *  - home_setting : pengaturan bernilai tunggal (judul, teks, gambar, warna, dll)
 *  - home_item    : konten berulang (kartu akses, galeri, misi, tupoksi, sasaran)
 *
 * Catatan: bila tabel belum ada (installasi baru), seluruh method baca akan
 * memakai nilai bawaan (blueprint) sehingga halaman depan tetap tampil normal.
 * Tabel dapat dibuat otomatis dari menu admin ("Buat Tabel Pengaturan").
 */
class HomeModel extends CI_Model
{
    /** nama tabel tanpa prefix (prefix diambil dari config/database.php) */
    protected $t_setting = 'home_setting';
    protected $t_item    = 'home_item';

    /** cache pengaturan gabungan (nilai DB + nilai bawaan) */
    private $settings_cache = null;

    /** cache item per grup */
    private $item_cache = array();

    public function __construct()
    {
        parent::__construct();
    }

    /* =====================================================================
     | BLUEPRINT
     | Definisi ini adalah "kontrak" halaman depan: key pengaturan, label pada
     | form admin, tipe input, dan nilai bawaannya.
     * ================================================================== */

    /**
     * Definisi grup pengaturan.
     *
     * @return array grup => array(judul, ikon, keterangan, fields)
     */
    public function blueprint()
    {
        return array(

            'umum' => array(
                'judul'      => 'Identitas Sistem',
                'ikon'       => 'md-view-compact',
                'keterangan' => 'Nama sistem, logo, dan warna tema halaman depan.',
                'fields'     => array(
                    'site_nama'      => array('label' => 'Nama Sistem', 'tipe' => 'text', 'default' => 'SIJAMU', 'bantuan' => 'Tampil pada navbar & footer'),
                    'site_subjudul'  => array('label' => 'Nama Panjang Sistem', 'tipe' => 'text', 'default' => 'Sistem Informasi Penjaminan Mutu'),
                    'site_institusi' => array('label' => 'Nama Institusi', 'tipe' => 'text', 'default' => 'STIK Siti Khadijah'),
                    'site_logo'      => array('label' => 'Logo', 'tipe' => 'image', 'default' => 'assets/assets/images/logostik.png', 'bantuan' => 'PNG transparan, tinggi maksimal 80px'),
                    'warna_primer'   => array('label' => 'Warna Utama', 'tipe' => 'color', 'default' => '#0f766e'),
                    'warna_aksen'    => array('label' => 'Warna Aksen', 'tipe' => 'color', 'default' => '#f59e0b'),
                ),
            ),

            'hero' => array(
                'judul'      => 'Bagian Hero',
                'ikon'       => 'md-image',
                'keterangan' => 'Banner utama yang pertama dilihat pengunjung.',
                'fields'     => array(
                    'hero_badge'      => array('label' => 'Label Kecil (badge)', 'tipe' => 'text', 'default' => 'Pusat Penjaminan Mutu'),
                    'hero_judul'      => array('label' => 'Judul Utama', 'tipe' => 'text', 'default' => 'SIJAMU'),
                    'hero_subjudul'   => array('label' => 'Sub Judul', 'tipe' => 'text', 'default' => 'Sistem Informasi Penjaminan Mutu'),
                    'hero_deskripsi'  => array('label' => 'Deskripsi', 'tipe' => 'textarea', 'default' => 'Media informasi, monitoring, dan evaluasi mutu untuk mendukung pelaksanaan Sistem Penjaminan Mutu Internal STIK Siti Khadijah secara terintegrasi, transparan, dan berkelanjutan dalam mewujudkan pendidikan tinggi yang bermutu.'),
                    'hero_gambar'     => array('label' => 'Ilustrasi Hero', 'tipe' => 'image', 'default' => 'assets/assets/images/sijamuhomepage.png'),
                    'hero_cta_label'  => array('label' => 'Tombol Utama - Teks', 'tipe' => 'text', 'default' => 'Pelajari Lebih Lanjut'),
                    'hero_cta_link'   => array('label' => 'Tombol Utama - Link', 'tipe' => 'text', 'default' => '#profil'),
                    'hero_cta2_label' => array('label' => 'Tombol Kedua - Teks', 'tipe' => 'text', 'default' => 'Masuk / Login'),
                    'hero_cta2_link'  => array('label' => 'Tombol Kedua - Link', 'tipe' => 'text', 'default' => 'login'),
                    'hero_catatan'    => array('label' => 'Catatan Kecil', 'tipe' => 'text', 'default' => 'Terintegrasi  •  Transparan  •  Berkelanjutan'),
                ),
            ),

            'akses' => array(
                'judul'      => 'Kartu Akses',
                'ikon'       => 'md-account-box',
                'keterangan' => 'Judul section dan kartu peran pengguna (PPM, Auditor, Auditee).',
                'fields'     => array(
                    'akses_judul'     => array('label' => 'Judul Section', 'tipe' => 'text', 'default' => 'Masuk Sesuai Peran Anda'),
                    'akses_deskripsi' => array('label' => 'Deskripsi', 'tipe' => 'textarea', 'default' => 'Setiap pengguna memiliki hak akses yang berbeda. Silakan masuk menggunakan akun yang telah diberikan oleh Pusat Penjaminan Mutu.'),
                ),
            ),

            'profil' => array(
                'judul'      => 'Profil',
                'ikon'       => 'md-info-outline',
                'keterangan' => 'Narasi profil Pusat Penjaminan Mutu.',
                'fields'     => array(
                    'profil_judul'     => array('label' => 'Judul Section', 'tipe' => 'text', 'default' => 'Profil Pusat Penjaminan Mutu'),
                    'profil_ringkas'   => array('label' => 'Paragraf Pembuka', 'tipe' => 'textarea', 'default' => '<strong>Pusat Penjaminan Mutu (PPM) STIK Siti Khadijah</strong> merupakan unit struktural yang bertanggung jawab dalam merencanakan, melaksanakan, mengevaluasi, mengendalikan, dan meningkatkan mutu penyelenggaraan Tridharma Perguruan Tinggi serta tata kelola institusi.'),
                    'profil_isi'       => array('label' => 'Paragraf Kedua', 'tipe' => 'textarea', 'default' => 'PPM berperan sebagai penggerak utama dalam implementasi <strong>Sistem Penjaminan Mutu Internal (SPMI)</strong> secara berkelanjutan. Dalam menjalankan fungsinya, PPM memastikan seluruh kegiatan akademik dan non-akademik berjalan sesuai standar yang ditetapkan, peraturan perundang-undangan, serta kebijakan nasional pendidikan tinggi. Selain itu, PPM menjadi pusat koordinasi pelaksanaan Audit Mutu Internal sebagai instrumen evaluasi dan peningkatan mutu institusi.'),
                ),
            ),

            'slider' => array(
                'judul'      => 'Galeri Profil',
                'ikon'       => 'md-collection-image-o',
                'keterangan' => 'Foto yang tampil berdampingan dengan narasi profil.',
                'fields'     => array(),
            ),

            'pengelola' => array(
                'judul'      => 'Pengelola',
                'ikon'       => 'md-accounts',
                'keterangan' => 'Struktur pengelola Pusat Penjaminan Mutu.',
                'fields'     => array(
                    'pengelola_judul'     => array('label' => 'Judul Section', 'tipe' => 'text', 'default' => 'Pengelola Pusat Penjaminan Mutu'),
                    'pengelola_deskripsi' => array('label' => 'Deskripsi', 'tipe' => 'textarea', 'default' => 'Susunan personel Pusat Penjaminan Mutu STIK Siti Khadijah yang bertugas menjalankan siklus penjaminan mutu internal.'),
                    'pengelola_gambar'    => array('label' => 'Gambar Struktur Pengelola', 'tipe' => 'image', 'default' => 'assets/assets/images/tim.png'),
                ),
            ),

            'struktur' => array(
                'judul'      => 'Struktur Organisasi',
                'ikon'       => 'md-device-hub',
                'keterangan' => 'Bagan struktur organisasi penjaminan mutu.',
                'fields'     => array(
                    'struktur_judul'     => array('label' => 'Judul Section', 'tipe' => 'text', 'default' => 'Struktur Organisasi'),
                    'struktur_deskripsi' => array('label' => 'Deskripsi', 'tipe' => 'textarea', 'default' => 'Bagan organisasi Pusat Penjaminan Mutu beserta garis koordinasi dengan unit kerja di lingkungan institusi.'),
                    'struktur_gambar'    => array('label' => 'Gambar Struktur Organisasi', 'tipe' => 'image', 'default' => 'assets/assets/images/strukturorganisasi_fix.png'),
                ),
            ),

            'visi' => array(
                'judul'      => 'Visi',
                'ikon'       => 'md-eye',
                'keterangan' => 'Visi Pusat Penjaminan Mutu.',
                'fields'     => array(
                    'visi_judul' => array('label' => 'Judul Section', 'tipe' => 'text', 'default' => 'Visi & Misi'),
                    'visi_isi'   => array('label' => 'Isi Visi', 'tipe' => 'textarea', 'default' => 'Menjadi pusat penjaminan mutu yang unggul dalam mengembangkan dan mengawal budaya mutu berkelanjutan guna mendukung terwujudnya <strong>STIK Siti Khadijah</strong> yang berkualitas dan berdaya saing.'),
                ),
            ),

            'misi' => array(
                'judul'      => 'Misi',
                'ikon'       => 'md-format-list-bulleted',
                'keterangan' => 'Daftar misi (urut dari atas ke bawah).',
                'fields'     => array(),
            ),

            'tupoksi' => array(
                'judul'      => 'Tugas Pokok & Fungsi',
                'ikon'       => 'md-assignment',
                'keterangan' => 'Daftar tugas pokok dan fungsi PPM.',
                'fields'     => array(
                    'tupoksi_judul' => array('label' => 'Judul Section', 'tipe' => 'text', 'default' => 'Tugas Pokok dan Fungsi'),
                ),
            ),

            'sasaran' => array(
                'judul'      => 'Sasaran Mutu',
                'ikon'       => 'md-flag',
                'keterangan' => 'Sasaran mutu umum dan khusus.',
                'fields'     => array(
                    'sasaran_judul'         => array('label' => 'Judul Section', 'tipe' => 'text', 'default' => 'Sasaran Mutu'),
                    'sasaran_umum_judul'    => array('label' => 'Judul Sasaran Umum', 'tipe' => 'text', 'default' => 'Sasaran Mutu - Umum'),
                    'sasaran_umum_isi'      => array('label' => 'Isi Sasaran Umum', 'tipe' => 'textarea', 'default' => 'Mewujudkan terlaksananya Sistem Penjaminan Mutu Internal (SPMI) secara konsisten dan berkelanjutan guna menjamin dan meningkatkan mutu penyelenggaraan Tridharma Perguruan Tinggi serta tata kelola institusi di lingkungan <strong>STIK Siti Khadijah</strong>.'),
                    'sasaran_khusus_judul'  => array('label' => 'Judul Sasaran Khusus', 'tipe' => 'text', 'default' => 'Sasaran Mutu - Khusus'),
                ),
            ),

            'dokumen' => array(
                'judul'      => 'Surat Keputusan',
                'ikon'       => 'md-file-text',
                'keterangan' => 'Judul section daftar SK (isinya dikelola pada menu Surat Keputusan).',
                'fields'     => array(
                    'sk_judul'     => array('label' => 'Judul Section', 'tipe' => 'text', 'default' => 'Surat Keputusan'),
                    'sk_deskripsi' => array('label' => 'Deskripsi', 'tipe' => 'textarea', 'default' => 'Dokumen surat keputusan yang berkaitan dengan penyelenggaraan penjaminan mutu.'),
                ),
            ),

            'berita' => array(
                'judul'      => 'Berita',
                'ikon'       => 'md-view-agenda',
                'keterangan' => 'Judul section berita (isinya dikelola pada menu Berita).',
                'fields'     => array(
                    'berita_judul'     => array('label' => 'Judul Section', 'tipe' => 'text', 'default' => 'Berita Terbaru'),
                    'berita_deskripsi' => array('label' => 'Deskripsi', 'tipe' => 'textarea', 'default' => 'Kabar terbaru seputar kegiatan penjaminan mutu.'),
                ),
            ),

            'pengumuman' => array(
                'judul'      => 'Pengumuman',
                'ikon'       => 'md-notifications',
                'keterangan' => 'Judul section pengumuman (isinya dikelola pada menu Pengumuman).',
                'fields'     => array(
                    'pengumuman_judul'     => array('label' => 'Judul Section', 'tipe' => 'text', 'default' => 'Pengumuman'),
                    'pengumuman_deskripsi' => array('label' => 'Deskripsi', 'tipe' => 'textarea', 'default' => 'Informasi resmi dari Pusat Penjaminan Mutu.'),
                ),
            ),

            'kontak' => array(
                'judul'      => 'Kontak & Footer',
                'ikon'       => 'md-phone',
                'keterangan' => 'Alamat, kontak, dan teks footer.',
                'fields'     => array(
                    'kontak_judul'   => array('label' => 'Judul Section', 'tipe' => 'text', 'default' => 'Hubungi Kami'),
                    'kontak_alamat'  => array('label' => 'Alamat', 'tipe' => 'textarea', 'default' => 'Jl. Demang Lebar Daun, Kelurahan Lorok Pakjo, Kecamatan Ilir Barat I, Kota Palembang, Sumatera Selatan 30137'),
                    'kontak_telepon' => array('label' => 'Telepon', 'tipe' => 'text', 'default' => '(0711) 315010'),
                    'kontak_email'   => array('label' => 'Email', 'tipe' => 'text', 'default' => 'spmi@stik-sitikhadijah.ac.id'),
                    'kontak_website' => array('label' => 'Website', 'tipe' => 'text', 'default' => 'https://stik-sitikhadijah.ac.id'),
                    'kontak_maps'    => array('label' => 'Embed Peta (opsional)', 'tipe' => 'textarea', 'default' => '', 'bantuan' => 'Tempel kode embed Google Maps (tag iframe), kosongkan bila tidak dipakai.'),
                    'footer_teks'    => array('label' => 'Teks Footer', 'tipe' => 'text', 'default' => 'Dikembangkan oleh STIK Siti Khadijah'),
                    'footer_kredit'  => array('label' => 'Teks Kredit', 'tipe' => 'text', 'default' => 'Pusat Penjaminan Mutu © ' . date('Y')),
                ),
            ),

            'section' => array(
                'judul'      => 'Tampilkan / Sembunyikan',
                'ikon'       => 'md-eye-off',
                'keterangan' => 'Atur section mana saja yang tampil di halaman depan.',
                'fields'     => array(
                    'section_statistik'  => array('label' => 'Statistik Singkat', 'tipe' => 'toggle', 'default' => '1'),
                    'section_kartu'      => array('label' => 'Kartu Akses / Login', 'tipe' => 'toggle', 'default' => '1'),
                    'section_profil'     => array('label' => 'Profil', 'tipe' => 'toggle', 'default' => '1'),
                    'section_visi'       => array('label' => 'Visi & Misi', 'tipe' => 'toggle', 'default' => '1'),
                    'section_tupoksi'    => array('label' => 'Tugas Pokok & Fungsi', 'tipe' => 'toggle', 'default' => '1'),
                    'section_sasaran'    => array('label' => 'Sasaran Mutu', 'tipe' => 'toggle', 'default' => '1'),
                    'section_pengelola'  => array('label' => 'Pengelola', 'tipe' => 'toggle', 'default' => '1'),
                    'section_struktur'   => array('label' => 'Struktur Organisasi', 'tipe' => 'toggle', 'default' => '1'),
                    'section_sk'         => array('label' => 'Surat Keputusan', 'tipe' => 'toggle', 'default' => '1'),
                    'section_berita'     => array('label' => 'Berita', 'tipe' => 'toggle', 'default' => '1'),
                    'section_pengumuman' => array('label' => 'Pengumuman', 'tipe' => 'toggle', 'default' => '1'),
                    'section_kontak'     => array('label' => 'Kontak', 'tipe' => 'toggle', 'default' => '1'),
                ),
            ),
        );
    }

    /**
     * Definisi grup konten berulang (home_item).
     *
     * @return array grup => array(judul, keterangan, fields, ikon)
     */
    public function item_blueprint()
    {
        return array(
            'kartu' => array(
                'judul'      => 'Kartu Akses',
                'keterangan' => 'Kartu peran pengguna pada halaman depan (PPM, Auditor, Auditee).',
                'ikon'       => 'md-account-box',
                'fields'     => array('judul', 'isi', 'ikon', 'gambar', 'warna', 'label_link', 'link', 'urutan', 'status'),
                'defaults'   => array(
                    array('judul' => 'PPM', 'isi' => 'Pusat Penjaminan Mutu - kelola dokumen, standar, dan siklus penjaminan mutu.', 'ikon' => 'bi-shield-check', 'warna' => '#0f766e', 'label_link' => 'Login sebagai PPM', 'link' => 'login', 'urutan' => 1),
                    array('judul' => 'Auditor', 'isi' => 'Anggota PPM yang melaksanakan audit mutu internal terhadap unit kerja.', 'ikon' => 'bi-clipboard-check', 'warna' => '#f59e0b', 'label_link' => 'Login sebagai Auditor', 'link' => 'login', 'urutan' => 2),
                    array('judul' => 'Auditee', 'isi' => 'Unit kerja atau program studi yang menjadi objek audit mutu internal.', 'ikon' => 'bi-people', 'warna' => '#dc2626', 'label_link' => 'Login sebagai Auditee', 'link' => 'login', 'urutan' => 3),
                ),
            ),
            'misi' => array(
                'judul'      => 'Misi',
                'keterangan' => 'Butir-butir misi Pusat Penjaminan Mutu.',
                'ikon'       => 'md-format-list-bulleted',
                'fields'     => array('isi', 'urutan', 'status'),
                'defaults'   => array(
                    array('isi' => 'Mengembangkan dan mengimplementasikan Sistem Penjaminan Mutu Internal (SPMI) secara konsisten dan berkelanjutan sesuai standar nasional pendidikan tinggi.', 'urutan' => 1),
                    array('isi' => 'Menyusun, menetapkan, dan mengembangkan standar mutu akademik dan non-akademik sebagai pedoman penyelenggaraan kegiatan institusi.', 'urutan' => 2),
                    array('isi' => 'Melaksanakan monitoring, evaluasi, dan Audit Mutu Internal (AMI) secara berkala terhadap program studi dan unit kerja.', 'urutan' => 3),
                    array('isi' => 'Mendorong terlaksananya siklus PPEPP (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan) sebagai budaya mutu di seluruh unit.', 'urutan' => 4),
                    array('isi' => 'Meningkatkan kompetensi sumber daya manusia dalam bidang penjaminan mutu melalui pelatihan, pendampingan, dan penyegaran auditor.', 'urutan' => 5),
                    array('isi' => 'Mendukung peningkatan mutu Tridharma Perguruan Tinggi dan tata kelola institusi dalam rangka pencapaian akreditasi yang unggul.', 'urutan' => 6),
                ),
            ),
            'tupoksi' => array(
                'judul'      => 'Tugas Pokok & Fungsi',
                'keterangan' => 'Butir tugas pokok dan fungsi.',
                'ikon'       => 'md-assignment',
                'fields'     => array('isi', 'urutan', 'status'),
                'defaults'   => array(
                    array('isi' => 'Merencanakan, melaksanakan, dan mengembangkan penyelenggaraan pendidikan berdasarkan peraturan dan pedoman sistem penjaminan mutu.', 'urutan' => 1),
                    array('isi' => 'Menyusun perangkat penjamin mutu.', 'urutan' => 2),
                    array('isi' => 'Memonitor dan mengevaluasi pelaksanaan penyelenggaraan Tridharma Perguruan Tinggi.', 'urutan' => 3),
                    array('isi' => 'Melaksanakan dan mengembangkan Audit Mutu Internal (AMI).', 'urutan' => 4),
                    array('isi' => 'Menyiapkan auditor Audit Mutu Internal.', 'urutan' => 5),
                    array('isi' => 'Menyelenggarakan rapat tinjauan manajemen.', 'urutan' => 6),
                    array('isi' => 'Memonitor dan mengevaluasi pelaksanaan hasil rapat tinjauan manajemen.', 'urutan' => 7),
                    array('isi' => 'Mendampingi proses akreditasi dan reakreditasi institusi, program studi, dan unit pelayanan pendidikan lainnya.', 'urutan' => 8),
                    array('isi' => 'Mengembangkan sistem informasi pendukung Sistem Penjaminan Mutu Internal (SPMI).', 'urutan' => 9),
                ),
            ),
            'sasaran' => array(
                'judul'      => 'Sasaran Mutu Khusus',
                'keterangan' => 'Butir sasaran mutu khusus.',
                'ikon'       => 'md-flag',
                'fields'     => array('isi', 'urutan', 'status'),
                'defaults'   => array(
                    array('isi' => 'Terlaksananya penyelenggaraan pendidikan sesuai dengan kebijakan dan standar SPMI.', 'urutan' => 1),
                    array('isi' => 'Tersusunnya perangkat penjaminan mutu yang lengkap dan mutakhir.', 'urutan' => 2),
                    array('isi' => 'Terlaksananya monitoring dan evaluasi Tridharma Perguruan Tinggi secara berkala.', 'urutan' => 3),
                    array('isi' => 'Terlaksananya Audit Mutu Internal (AMI) secara sistematis dan berkelanjutan.', 'urutan' => 4),
                    array('isi' => 'Tersedianya auditor mutu internal yang kompeten.', 'urutan' => 5),
                    array('isi' => 'Terselenggaranya Rapat Tinjauan Manajemen (RTM) secara rutin.', 'urutan' => 6),
                    array('isi' => 'Terlaksananya tindak lanjut hasil Rapat Tinjauan Manajemen.', 'urutan' => 7),
                    array('isi' => 'Terlaksananya pendampingan akreditasi dan reakreditasi secara optimal.', 'urutan' => 8),
                    array('isi' => 'Berkembangnya sistem informasi pendukung SPMI yang efektif.', 'urutan' => 9),
                ),
            ),
            'slider' => array(
                'judul'      => 'Galeri Foto',
                'keterangan' => 'Foto kegiatan yang tampil pada bagian profil.',
                'ikon'       => 'md-collection-image-o',
                'fields'     => array('judul', 'gambar', 'urutan', 'status'),
                'defaults'   => array(
                    array('judul' => 'Personel Pusat Penjaminan Mutu', 'gambar' => 'assets/assets/images/personil.jpg', 'urutan' => 1),
                    array('judul' => 'Kegiatan Audit Mutu Internal', 'gambar' => 'assets/assets/images/personil2.jpg', 'urutan' => 2),
                ),
            ),
        );
    }

    /* =====================================================================
     | STATUS TABEL
     * ================================================================== */

    /**
     * Apakah kedua tabel pengaturan sudah tersedia?
     */
    public function installed()
    {
        return $this->db->table_exists($this->t_setting) && $this->db->table_exists($this->t_item);
    }

    /**
     * Buat tabel + isi nilai bawaan (dipanggil dari menu admin).
     *
     * @return bool
     */
    public function install()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists($this->t_setting)) {
            $this->dbforge->add_field(array(
                'setting_id'     => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'setting_kunci'  => array('type' => 'VARCHAR', 'constraint' => 100),
                'setting_grup'   => array('type' => 'VARCHAR', 'constraint' => 50, 'default' => 'umum'),
                'setting_label'  => array('type' => 'VARCHAR', 'constraint' => 150, 'null' => TRUE),
                'setting_tipe'   => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => 'text'),
                'setting_nilai'  => array('type' => 'TEXT', 'null' => TRUE),
                'setting_urutan' => array('type' => 'INT', 'constraint' => 11, 'default' => 0),
                'setting_update' => array('type' => 'DATETIME', 'null' => TRUE),
            ));
            $this->dbforge->add_key('setting_id', TRUE);
            $this->dbforge->add_key('setting_kunci');
            $this->dbforge->create_table($this->t_setting, TRUE);
        }

        if (!$this->db->table_exists($this->t_item)) {
            $this->dbforge->add_field(array(
                'item_id'         => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'item_grup'       => array('type' => 'VARCHAR', 'constraint' => 50),
                'item_judul'      => array('type' => 'VARCHAR', 'constraint' => 200, 'null' => TRUE),
                'item_isi'        => array('type' => 'TEXT', 'null' => TRUE),
                'item_gambar'     => array('type' => 'VARCHAR', 'constraint' => 200, 'null' => TRUE),
                'item_ikon'       => array('type' => 'VARCHAR', 'constraint' => 60, 'null' => TRUE),
                'item_link'       => array('type' => 'VARCHAR', 'constraint' => 200, 'null' => TRUE),
                'item_label_link' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE),
                'item_warna'      => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => TRUE),
                'item_urutan'     => array('type' => 'INT', 'constraint' => 11, 'default' => 0),
                'item_status'     => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1),
                'item_create'     => array('type' => 'DATETIME', 'null' => TRUE),
                'item_update'     => array('type' => 'DATETIME', 'null' => TRUE),
            ));
            $this->dbforge->add_key('item_id', TRUE);
            $this->dbforge->add_key('item_grup');
            $this->dbforge->create_table($this->t_item, TRUE);
        }

        $this->seed_defaults();

        $this->settings_cache = null;
        $this->item_cache = array();

        return $this->installed();
    }

    /**
     * Isi nilai bawaan untuk key/grup yang belum ada (tidak menimpa data lama).
     */
    public function seed_defaults()
    {
        $now = date('Y-m-d H:i:s');
        $urutan = 0;

        foreach ($this->blueprint() as $grup => $bagian) {
            foreach ($bagian['fields'] as $kunci => $field) {
                $urutan++;

                if ($this->db->where('setting_kunci', $kunci)->count_all_results($this->t_setting) > 0) {
                    continue;
                }

                $this->db->insert($this->t_setting, array(
                    'setting_kunci'  => $kunci,
                    'setting_grup'   => $grup,
                    'setting_label'  => $field['label'],
                    'setting_tipe'   => $field['tipe'],
                    'setting_nilai'  => isset($field['default']) ? $field['default'] : '',
                    'setting_urutan' => $urutan,
                    'setting_update' => $now,
                ));
            }
        }

        foreach ($this->item_blueprint() as $grup => $bagian) {
            if ($this->db->where('item_grup', $grup)->count_all_results($this->t_item) > 0) {
                continue;
            }

            // default_items() menormalkan nama kolom (judul/isi/gambar -> item_*)
            foreach ($this->default_items($grup) as $item) {
                $item['item_grup'] = $grup;
                $this->add_item($item);
            }
        }

        return TRUE;
    }

    /* =====================================================================
     | BACA PENGATURAN
     * ================================================================== */

    /**
     * Nilai bawaan seluruh pengaturan: array(kunci => nilai).
     */
    public function default_settings()
    {
        $out = array();

        foreach ($this->blueprint() as $grup => $bagian) {
            foreach ($bagian['fields'] as $kunci => $field) {
                $out[$kunci] = isset($field['default']) ? $field['default'] : '';
            }
        }

        return $out;
    }

    /**
     * Seluruh pengaturan (nilai DB ditimpa di atas nilai bawaan).
     *
     * @return array kunci => nilai
     */
    public function get_settings()
    {
        if ($this->settings_cache !== NULL) {
            return $this->settings_cache;
        }

        $settings = $this->default_settings();

        if ($this->db->table_exists($this->t_setting)) {
            $rows = $this->db->get($this->t_setting)->result();

            foreach ($rows as $row) {
                if (!array_key_exists($row->setting_kunci, $settings)) {
                    continue; // key di luar blueprint diabaikan
                }

                $settings[$row->setting_kunci] = $row->setting_nilai;
            }
        }

        $this->settings_cache = $settings;

        return $settings;
    }

    /**
     * Ambil satu nilai pengaturan (memakai nilai bawaan bila kosong).
     */
    public function val($kunci, $default = '')
    {
        $settings = $this->get_settings();
        $nilai = isset($settings[$kunci]) ? $settings[$kunci] : $default;

        if ($nilai === '' || $nilai === NULL || $nilai === FALSE) {
            $bawaan = $this->default_settings();
            $nilai = isset($bawaan[$kunci]) ? $bawaan[$kunci] : $default;
        }

        return $nilai;
    }

    /**
     * URL gambar/media dari pengaturan (atau bawaan bila kosong).
     */
    public function media($kunci, $default = '')
    {
        $nilai = $this->val($kunci, $default);

        if ($nilai === '' || $nilai === NULL) {
            return $default;
        }

        if (preg_match('#^(https?:)?//#i', $nilai)) {
            return $nilai;
        }

        return base_url(ltrim($nilai, '/'));
    }

    /**
     * Apakah sebuah section ditampilkan? (setting tipe toggle)
     */
    public function on($kunci)
    {
        $nilai = $this->val($kunci, '1');

        return !($nilai === '0' || $nilai === 0 || $nilai === '' || $nilai === FALSE || $nilai === 'off');
    }

    /**
     * Pengaturan dikelompokkan per grup untuk halaman admin.
     *
     * @return array grup => array(bagian => ..., rows => array(field => baris DB))
     */
    public function get_settings_grouped($hanya_terisi = FALSE)
    {
        $db_rows = array();

        if ($this->db->table_exists($this->t_setting)) {
            foreach ($this->db->get($this->t_setting)->result() as $row) {
                $db_rows[$row->setting_kunci] = $row;
            }
        }

        $out = array();

        foreach ($this->blueprint() as $grup => $bagian) {
            $rows = array();

            foreach ($bagian['fields'] as $kunci => $field) {
                if ($hanya_terisi && !isset($db_rows[$kunci])) {
                    continue;
                }

                $row = isset($db_rows[$kunci]) ? $db_rows[$kunci] : NULL;
                $rows[$kunci] = array(
                    'kunci'   => $kunci,
                    'label'   => $field['label'],
                    'tipe'    => $field['tipe'],
                    'bantuan' => isset($field['bantuan']) ? $field['bantuan'] : '',
                    'nilai'   => ($row && $row->setting_nilai !== NULL && $row->setting_nilai !== '')
                        ? $row->setting_nilai
                        : (isset($field['default']) ? $field['default'] : ''),
                    'tersimpan' => $row ? $row->setting_nilai : NULL,
                    'update'    => $row ? $row->setting_update : NULL,
                );
            }

            $out[$grup] = array(
                'judul'      => $bagian['judul'],
                'ikon'       => $bagian['ikon'],
                'keterangan' => $bagian['keterangan'],
                'rows'       => $rows,
            );
        }

        return $out;
    }

    /**
     * Simpan banyak pengaturan sekaligus.
     *
     * @param array $data kunci => nilai
     * @return int jumlah baris yang disimpan
     */
    public function save_settings($data)
    {
        $bawaan = $this->default_settings();
        $now = date('Y-m-d H:i:s');
        $tersimpan = 0;

        foreach ($data as $kunci => $nilai) {
            if (!array_key_exists($kunci, $bawaan)) {
                continue; // tolak key yang tidak dikenal
            }

            $row = $this->db->get_where($this->t_setting, array('setting_kunci' => $kunci))->row();

            if ($row) {
                $this->db->where('setting_kunci', $kunci);
                $this->db->update($this->t_setting, array(
                    'setting_nilai'  => $nilai,
                    'setting_update' => $now,
                ));
            } else {
                $this->db->insert($this->t_setting, array(
                    'setting_kunci'  => $kunci,
                    'setting_nilai'  => $nilai,
                    'setting_grup'   => $this->grup_of($kunci),
                    'setting_label'  => $this->label_of($kunci),
                    'setting_tipe'   => $this->tipe_of($kunci),
                    'setting_urutan' => 0,
                    'setting_update' => $now,
                ));
            }

            $tersimpan++;
        }

        $this->settings_cache = null;

        return $tersimpan;
    }

    /* ---- helper kecil untuk metadata key -------------------------------- */

    private function field_of($kunci)
    {
        foreach ($this->blueprint() as $grup => $bagian) {
            if (isset($bagian['fields'][$kunci])) {
                return array('grup' => $grup, 'field' => $bagian['fields'][$kunci], 'judul' => $bagian['judul']);
            }
        }

        return NULL;
    }

    public function grup_of($kunci)
    {
        $f = $this->field_of($kunci);

        return $f ? $f['grup'] : 'umum';
    }

    public function label_of($kunci)
    {
        $f = $this->field_of($kunci);

        return $f ? $f['field']['label'] : $kunci;
    }

    public function tipe_of($kunci)
    {
        $f = $this->field_of($kunci);

        return $f ? $f['field']['tipe'] : 'text';
    }

    /* =====================================================================
     | BACA ITEM (konten berulang)
     * ================================================================== */

    /**
     * Ambil item berdasarkan grup.
     *
     * @param string $grup
     * @param bool   $hanya_aktif
     * @return array
     */
    public function get_items($grup, $hanya_aktif = FALSE, $pakai_bawaan = TRUE)
    {
        $key = $grup . ($hanya_aktif ? '_aktif' : '') . ($pakai_bawaan ? '' : '_db');

        if (isset($this->item_cache[$key])) {
            return $this->item_cache[$key];
        }

        $items = $this->query_items($grup);

        // Fallback: grup belum pernah diisi (atau tabel belum dibuat)
        // -> tampilkan konten bawaan supaya halaman depan tidak kosong.
        // Halaman admin memakai $pakai_bawaan = FALSE agar yang tampil
        // hanya data yang benar-benar tersimpan di database.
        if (empty($items) && $pakai_bawaan) {
            $items = $this->default_items($grup);
        }

        if ($hanya_aktif) {
            $aktif = array();

            foreach ($items as $item) {
                if (isset($item['item_status']) && (int) $item['item_status'] === 1) {
                    $aktif[] = $item;
                }
            }

            $items = $aktif;
        }

        $this->item_cache[$key] = $items;

        return $items;
    }

    /**
     * Ambil item sebuah grup langsung dari tabel (tanpa konten bawaan).
     *
     * @return array
     */
    private function query_items($grup)
    {
        if (!$this->db->table_exists($this->t_item)) {
            return array();
        }

        $this->db->from($this->t_item);
        $this->db->where('item_grup', $grup);
        $this->db->order_by('item_urutan', 'ASC');
        $this->db->order_by('item_id', 'ASC');

        return $this->db->get()->result_array();
    }

    /**
     * Konten bawaan untuk sebuah grup (dipakai bila isi tabel belum ada).
     *
     * @return array
     */
    public function default_items($grup)
    {
        $blueprint = $this->item_blueprint();
        $items = array();

        if (!isset($blueprint[$grup])) {
            return $items;
        }

        foreach ($blueprint[$grup]['defaults'] as $default) {
            $items[] = array(
                'item_id'         => 0,
                'item_grup'       => $grup,
                'item_judul'      => isset($default['judul']) ? $default['judul'] : '',
                'item_isi'        => isset($default['isi']) ? $default['isi'] : '',
                'item_gambar'     => isset($default['gambar']) ? $default['gambar'] : '',
                'item_ikon'       => isset($default['ikon']) ? $default['ikon'] : '',
                'item_link'       => isset($default['link']) ? $default['link'] : '',
                'item_label_link' => isset($default['label_link']) ? $default['label_link'] : '',
                'item_warna'      => isset($default['warna']) ? $default['warna'] : '',
                'item_urutan'     => isset($default['urutan']) ? $default['urutan'] : 0,
                'item_status'     => 1,
            );
        }

        return $items;
    }

    /**
     * URL media untuk sebuah item (gambar item atau kosong).
     */
    public function item_media($item, $default = '')
    {
        $gambar = isset($item['item_gambar']) ? $item['item_gambar'] : '';

        if ($gambar === '' || $gambar === NULL) {
            return $default;
        }

        if (preg_match('#^(https?:)?//#i', $gambar)) {
            return $gambar;
        }

        return base_url(ltrim($gambar, '/'));
    }

    /**
     * Ambil satu item berdasarkan id.
     */
    public function get_item($id)
    {
        return $this->db->get_where($this->t_item, array('item_id' => (int) $id))->row_array();
    }

    /**
     * Berapa jumlah item dalam satu grup (dipakai halaman admin).
     */
    public function count_items($grup)
    {
        if (!$this->db->table_exists($this->t_item)) {
            return 0;
        }

        return (int) $this->db->where('item_grup', $grup)->count_all_results($this->t_item);
    }

    /**
     * Statistik singkat halaman depan (diambil langsung dari database).
     *
     * @return array daftar array('label' => ..., 'nilai' => int, 'ikon' => ...)
     */
    public function statistik()
    {
        $kandidat = array(
            array('tabel' => 'data', 'label' => 'Dokumen Mutu', 'ikon' => 'bi-folder2-open'),
            array('tabel' => 'sk', 'label' => 'Surat Keputusan', 'ikon' => 'bi-file-earmark-text'),
            array('tabel' => 'berita', 'label' => 'Berita', 'ikon' => 'bi-newspaper'),
            array('tabel' => 'pengumuman', 'label' => 'Pengumuman', 'ikon' => 'bi-megaphone'),
        );

        $out = array();

        foreach ($kandidat as $stat) {
            if (!$this->db->table_exists($stat['tabel'])) {
                continue;
            }

            $out[] = array(
                'label' => $stat['label'],
                'ikon'  => $stat['ikon'],
                'nilai' => (int) $this->db->count_all($stat['tabel']),
            );
        }

        if ($this->db->table_exists('users')) {
            $out[] = array(
                'label' => 'Auditor Mutu',
                'ikon'  => 'bi-person-badge',
                'nilai' => (int) $this->db->where('role', 'AUDITOR')->count_all_results('users'),
            );
        }

        return $out;
    }

    /* =====================================================================
     | TULIS ITEM
     * ================================================================== */

    /**
     * Tambah item baru.
     */
    public function add_item($data)
    {
        $now = date('Y-m-d H:i:s');

        $this->db->insert($this->t_item, array(
            'item_grup'       => $data['item_grup'],
            'item_judul'      => isset($data['item_judul']) ? $data['item_judul'] : '',
            'item_isi'        => isset($data['item_isi']) ? $data['item_isi'] : '',
            'item_gambar'     => isset($data['item_gambar']) ? $data['item_gambar'] : '',
            'item_ikon'       => isset($data['item_ikon']) ? $data['item_ikon'] : '',
            'item_link'       => isset($data['item_link']) ? $data['item_link'] : '',
            'item_label_link' => isset($data['item_label_link']) ? $data['item_label_link'] : '',
            'item_warna'      => isset($data['item_warna']) ? $data['item_warna'] : '',
            'item_urutan'     => isset($data['item_urutan']) ? (int) $data['item_urutan'] : 0,
            'item_status'     => isset($data['item_status']) ? (int) $data['item_status'] : 1,
            'item_create'     => $now,
            'item_update'     => $now,
        ));

        $this->item_cache = array();

        return $this->db->insert_id();
    }

    /**
     * Ubah item (hanya kolom yang dikirim yang diupdate).
     */
    public function edit_item($data)
    {
        if (empty($data['item_id'])) {
            return FALSE;
        }

        $update = array('item_update' => date('Y-m-d H:i:s'));

        foreach (array('item_grup', 'item_judul', 'item_isi', 'item_gambar', 'item_ikon', 'item_link', 'item_label_link', 'item_warna') as $kolom) {
            if (array_key_exists($kolom, $data)) {
                $update[$kolom] = $data[$kolom];
            }
        }

        foreach (array('item_urutan', 'item_status') as $kolom) {
            if (array_key_exists($kolom, $data)) {
                $update[$kolom] = (int) $data[$kolom];
            }
        }

        $this->db->where('item_id', (int) $data['item_id']);
        $this->db->update($this->t_item, $update);

        $this->item_cache = array();

        return $this->db->affected_rows() >= 0;
    }

    /**
     * Hapus item.
     */
    public function delete_item($id)
    {
        $this->db->where('item_id', (int) $id);
        $this->db->delete($this->t_item);

        $this->item_cache = array();

        return $this->db->affected_rows() > 0;
    }
}
