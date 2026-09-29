<?php
/**
 * Halaman depan SIJAMU (redesign).
 *
 * Seluruh teks, gambar, warna, dan section dapat diatur dari panel admin:
 *   Website > Pengaturan Home
 *
 * Data yang tersedia:
 *   $home       : HomeModel (pengaturan + item)
 *   $berita     : daftar berita terbaru
 *   $pengumuman : daftar pengumuman terbaru
 *   $sk         : daftar surat keputusan
 *   $statistik  : statistik singkat (dihitung dari database)
 */

/* ---------- helper tampilan ---------- */
if (!function_exists('hp_shade')) {
    /** Terangkan (delta > 0) atau gelapkan (delta < 0) sebuah warna hex. */
    function hp_shade($hex, $delta)
    {
        $hex = ltrim(trim((string) $hex), '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (!preg_match('/^[0-9a-f]{6}$/i', $hex)) {
            $hex = '0f766e';
        }

        $out = '#';

        for ($i = 0; $i < 3; $i++) {
            $c = hexdec(substr($hex, $i * 2, 2));
            $c = max(0, min(255, (int) round($c + ($c * $delta))));
            $out .= str_pad(dechex($c), 2, '0', STR_PAD_LEFT);
        }

        return $out;
    }
}

if (!function_exists('hp_tautan')) {
    /** Ubah nilai pengaturan menjadi URL: anchor & URL absolut dibiarkan apa adanya. */
    function hp_tautan($nilai, $bawaan = '#')
    {
        $nilai = trim((string) $nilai);

        if ($nilai === '') {
            return $bawaan;
        }

        if ($nilai[0] === '#' || preg_match('#^(https?:)?//#i', $nilai)) {
            return $nilai;
        }

        return base_url($nilai);
    }
}

if (!function_exists('hp_potong')) {
    /** Potong teks berdasarkan jumlah kata. */
    function hp_potong($teks, $jumlah = 22)
    {
        $teks = trim(preg_replace('/\s+/', ' ', strip_tags((string) $teks)));

        if ($teks === '') {
            return '';
        }

        $kata = explode(' ', $teks);

        if (count($kata) <= $jumlah) {
            return $teks;
        }

        return implode(' ', array_slice($kata, 0, $jumlah)) . ' ...';
    }
}

if (!function_exists('hp_tanggal')) {
    /** Pecah tanggal database menjadi array(hari, bulan, tahun, lengkap). */
    function hp_tanggal($nilai, $format = 'd M Y')
    {
        $waktu = strtotime((string) $nilai);

        if (!$waktu) {
            return array('hari' => '-', 'bulan' => '', 'tahun' => '', 'lengkap' => '');
        }

        $bulan = array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des');

        return array(
            'hari'    => date('d', $waktu),
            'bulan'   => $bulan[(int) date('n', $waktu)],
            'tahun'   => date('Y', $waktu),
            'lengkap' => date($format, $waktu),
        );
    }
}

/* ---------- data siap pakai ---------- */
$kartu   = $home->get_items('kartu', TRUE);
$galeri  = $home->get_items('slider', TRUE);
$misi    = $home->get_items('misi', TRUE);
$tupoksi = $home->get_items('tupoksi', TRUE);
$sasaran = $home->get_items('sasaran', TRUE);

$berita     = isset($berita) ? $berita : array();
$pengumuman = isset($pengumuman) ? $pengumuman : array();
$sk         = isset($sk) ? $sk : array();
$statistik  = isset($statistik) ? $statistik : array();

$warna_primer = $home->val('warna_primer', '#0f766e');
$warna_aksen  = $home->val('warna_aksen', '#f59e0b');

$institusi = $home->val('site_institusi', 'STIK Siti Khadijah');
$nama      = $home->val('site_nama', 'SIJAMU');

/* menu navigasi: section -> label (hanya yang aktif) */
$menu_section = array(
    array('id' => 'beranda', 'label' => 'Beranda', 'tampil' => TRUE),
    array('id' => 'profil', 'label' => 'Profil', 'tampil' => $home->on('section_profil')),
    array('id' => 'visi-misi', 'label' => 'Visi & Misi', 'tampil' => $home->on('section_visi')),
    array('id' => 'tupoksi', 'label' => 'Tupoksi', 'tampil' => $home->on('section_tupoksi')),
    array('id' => 'sasaran-mutu', 'label' => 'Sasaran Mutu', 'tampil' => $home->on('section_sasaran')),
    array('id' => 'pengelola', 'label' => 'Pengelola', 'tampil' => $home->on('section_pengelola')),
    array('id' => 'struktur', 'label' => 'Struktur Organisasi', 'tampil' => $home->on('section_struktur')),
    array('id' => 'sk', 'label' => 'Surat Keputusan', 'tampil' => $home->on('section_sk')),
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php echo htmlspecialchars($nama); ?> - <?php echo htmlspecialchars($home->val('site_subjudul', '')); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars(hp_potong($home->val('hero_deskripsi', ''), 30)); ?>">
  <meta name="keywords" content="SPMI, penjaminan mutu, audit mutu internal, <?php echo htmlspecialchars($institusi); ?>">
  <meta name="author" content="<?php echo htmlspecialchars($institusi); ?>">
  <meta property="og:title" content="<?php echo htmlspecialchars($nama . ' - ' . $home->val('site_subjudul', '')); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars(hp_potong($home->val('hero_deskripsi', ''), 30)); ?>">
  <meta property="og:type" content="website">
  <link rel="icon" type="image/png" href="<?php echo $home->media('site_logo', base_url('assets/assets/images/logostik.png')); ?>">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Gaya halaman depan -->
  <link rel="stylesheet" href="<?php echo asset_url(); ?>desain/home.css?v=1.0.0">

  <style>
    :root {
      --hp-primer: <?php echo htmlspecialchars($warna_primer); ?>;
      --hp-primer-dark: <?php echo htmlspecialchars(hp_shade($warna_primer, -0.28)); ?>;
      --hp-primer-deep: <?php echo htmlspecialchars(hp_shade($warna_primer, -0.55)); ?>;
      --hp-primer-light: <?php echo htmlspecialchars(hp_shade($warna_primer, 0.25)); ?>;
      --hp-aksen: <?php echo htmlspecialchars($warna_aksen); ?>;
    }
  </style>
</head>
<body>

<!-- ================= NAVBAR ================= -->
<header class="hp-nav" id="hpNav">
  <nav class="navbar navbar-expand-lg">
    <div class="container">

      <a class="hp-nav__brand" href="<?php echo base_url(); ?>">
        <img src="<?php echo $home->media('site_logo', base_url('assets/assets/images/logostik.png')); ?>" alt="Logo <?php echo htmlspecialchars($institusi); ?>">
        <span class="hp-nav__brand-text">
          <strong><?php echo htmlspecialchars($nama); ?></strong>
          <span><?php echo htmlspecialchars($home->val('site_subjudul', '')); ?></span>
        </span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#hpMenu" aria-controls="hpMenu" aria-expanded="false" aria-label="Buka menu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="hpMenu">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item">
            <a class="nav-link hp-nav__link" data-target="beranda" href="#beranda">Beranda</a>
          </li>

          <?php if ($home->on('section_profil') || $home->on('section_visi') || $home->on('section_tupoksi') || $home->on('section_sasaran') || $home->on('section_pengelola') || $home->on('section_struktur') || $home->on('section_sk')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link hp-nav__link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Tentang Kami
            </a>
            <ul class="dropdown-menu">
              <?php foreach ($menu_section as $menu): ?>
                <?php if ($menu['id'] === 'beranda' || !$menu['tampil']) { continue; } ?>
                <li><a class="dropdown-item" href="#<?php echo $menu['id']; ?>"><?php echo htmlspecialchars($menu['label']); ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>
          <?php endif; ?>

          <li class="nav-item dropdown">
            <a class="nav-link hp-nav__link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              SPMI
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?php echo base_url('penetapan'); ?>">Penetapan</a></li>
              <li><a class="dropdown-item" href="<?php echo base_url('pelaksanaan'); ?>">Pelaksanaan</a></li>
              <li><a class="dropdown-item" href="<?php echo base_url('evaluasi'); ?>">Evaluasi</a></li>
              <li><a class="dropdown-item" href="<?php echo base_url('pengendalian'); ?>">Pengendalian</a></li>
              <li><a class="dropdown-item" href="<?php echo base_url('peningkatan'); ?>">Peningkatan</a></li>
            </ul>
          </li>

          <?php if ($home->on('section_berita') || $home->on('section_pengumuman')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link hp-nav__link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Informasi
            </a>
            <ul class="dropdown-menu">
              <?php if ($home->on('section_berita')): ?>
                <li><a class="dropdown-item" href="#berita"><?php echo htmlspecialchars($home->val('berita_judul', 'Berita')); ?></a></li>
              <?php endif; ?>
              <?php if ($home->on('section_pengumuman')): ?>
                <li><a class="dropdown-item" href="#pengumuman"><?php echo htmlspecialchars($home->val('pengumuman_judul', 'Pengumuman')); ?></a></li>
              <?php endif; ?>
            </ul>
          </li>
          <?php endif; ?>

          <?php if ($home->on('section_kontak')): ?>
          <li class="nav-item">
            <a class="nav-link hp-nav__link" data-target="kontak" href="#kontak">Kontak</a>
          </li>
          <?php endif; ?>

          <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
            <a class="hp-btn hp-btn--primer hp-nav__cta" href="<?php echo base_url('login'); ?>">
              <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
          </li>
        </ul>
      </div>

    </div>
  </nav>
</header>

<main id="konten">

  <!-- ================= HERO ================= -->
  <section class="hp-hero" id="beranda">
    <div class="container">
      <div class="row align-items-center g-5">

        <div class="col-lg-6">
          <?php if ($home->val('hero_badge') !== ''): ?>
            <span class="hp-hero__badge"><i class="bi bi-patch-check-fill"></i> <?php echo htmlspecialchars($home->val('hero_badge')); ?></span>
          <?php endif; ?>

          <h1 class="hp-hero__title">
            <?php echo htmlspecialchars($home->val('hero_judul', $nama)); ?>
            <span class="hp-hero__subjudul"><?php echo htmlspecialchars($home->val('hero_subjudul', '')); ?></span>
          </h1>

          <p class="hp-hero__text"><?php echo nl2br(htmlspecialchars($home->val('hero_deskripsi', ''))); ?></p>

          <div class="hp-hero__cta">
            <?php if ($home->val('hero_cta_label') !== ''): ?>
              <a class="hp-btn hp-btn--aksen" href="<?php echo hp_tautan($home->val('hero_cta_link'), '#profil'); ?>">
                <?php echo htmlspecialchars($home->val('hero_cta_label')); ?> <i class="bi bi-arrow-right"></i>
              </a>
            <?php endif; ?>
            <?php if ($home->val('hero_cta2_label') !== ''): ?>
              <a class="hp-btn hp-btn--outline" href="<?php echo hp_tautan($home->val('hero_cta2_link'), base_url('login')); ?>">
                <i class="bi bi-box-arrow-in-right"></i> <?php echo htmlspecialchars($home->val('hero_cta2_label')); ?>
              </a>
            <?php endif; ?>
          </div>

          <?php if ($home->val('hero_catatan') !== ''): ?>
            <p class="hp-hero__note"><?php echo htmlspecialchars($home->val('hero_catatan')); ?></p>
          <?php endif; ?>
        </div>

        <div class="col-lg-6">
          <div class="hp-hero__visual" data-reveal>
            <img src="<?php echo $home->media('hero_gambar'); ?>" alt="<?php echo htmlspecialchars($nama); ?>">
            <div class="hp-hero__card">
              <i class="bi bi-graph-up-arrow"></i>
              <span>
                <strong>SPMI PPEPP</strong>
                <span>Penetapan • Pelaksanaan • Evaluasi • Pengendalian • Peningkatan</span>
              </span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ================= STATISTIK ================= -->
  <?php if ($home->on('section_statistik') && !empty($statistik)): ?>
  <section class="hp-stats" id="statistik">
    <div class="container">
      <div class="hp-stats__grid" data-reveal>
        <?php foreach ($statistik as $stat): ?>
          <div class="hp-stat">
            <div class="hp-stat__icon"><i class="bi <?php echo htmlspecialchars($stat['ikon']); ?>"></i></div>
            <div>
              <div class="hp-stat__value"><?php echo number_format($stat['nilai'], 0, ',', '.'); ?></div>
              <div class="hp-stat__label"><?php echo htmlspecialchars($stat['label']); ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ================= KARTU AKSES ================= -->
  <?php if ($home->on('section_kartu') && !empty($kartu)): ?>
  <section class="hp-section" id="fitur">
    <div class="container">
      <div class="text-center mb-5" data-reveal>
        <span class="hp-eyebrow">Akses Sistem</span>
        <h2 class="hp-title"><?php echo htmlspecialchars($home->val('akses_judul', 'Masuk Sesuai Peran Anda')); ?></h2>
        <p class="hp-subtitle"><?php echo htmlspecialchars($home->val('akses_deskripsi', '')); ?></p>
        <div class="hp-rule"></div>
      </div>

      <div class="row g-4 justify-content-center">
        <?php foreach ($kartu as $item): ?>
          <div class="col-md-6 col-lg-4" data-reveal>
            <div class="hp-kartu" style="--kartu-warna: <?php echo htmlspecialchars($item['item_warna'] !== '' ? $item['item_warna'] : $warna_primer); ?>;">
              <div class="hp-kartu__icon">
                <?php if (!empty($item['item_gambar'])): ?>
                  <img src="<?php echo $home->item_media($item); ?>" alt="<?php echo htmlspecialchars($item['item_judul']); ?>">
                <?php else: ?>
                  <i class="bi <?php echo htmlspecialchars($item['item_ikon'] !== '' ? $item['item_ikon'] : 'bi-person-circle'); ?>"></i>
                <?php endif; ?>
              </div>
              <h3 class="hp-kartu__title"><?php echo htmlspecialchars($item['item_judul']); ?></h3>
              <p class="hp-kartu__text"><?php echo nl2br(htmlspecialchars($item['item_isi'])); ?></p>
              <?php if (!empty($item['item_link'])): ?>
                <a class="hp-kartu__btn" href="<?php echo hp_tautan($item['item_link'], base_url('login')); ?>">
                  <?php echo htmlspecialchars($item['item_label_link'] !== '' ? $item['item_label_link'] : 'Selengkapnya'); ?>
                  <i class="bi bi-arrow-right"></i>
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ================= PROFIL ================= -->
  <?php if ($home->on('section_profil')): ?>
  <section class="hp-section hp-section--soft" id="profil">
    <div class="container">
      <div class="row g-5 align-items-center">

        <div class="col-lg-6" data-reveal>
          <span class="hp-eyebrow">Tentang Kami</span>
          <h2 class="hp-title"><?php echo htmlspecialchars($home->val('profil_judul')); ?></h2>
          <div class="hp-rule hp-rule--left mb-4"></div>

          <div class="hp-profil__body text-secondary">
            <p><?php echo $home->val('profil_ringkas'); ?></p>
            <p class="mb-0"><?php echo $home->val('profil_isi'); ?></p>
          </div>

          <div class="hp-pill-list">
            <span class="hp-pill"><i class="bi bi-check2-circle"></i> Siklus PPEPP</span>
            <span class="hp-pill"><i class="bi bi-check2-circle"></i> Audit Mutu Internal</span>
            <span class="hp-pill"><i class="bi bi-check2-circle"></i> Standar Mutu</span>
            <span class="hp-pill"><i class="bi bi-check2-circle"></i> Akreditasi</span>
          </div>
        </div>

        <div class="col-lg-6" data-reveal>
          <?php if (!empty($galeri)): ?>
            <div id="hpGaleri" class="carousel slide hp-profil__media" data-bs-ride="carousel">
              <div class="carousel-indicators">
                <?php foreach ($galeri as $i => $gambar): ?>
                  <button type="button" data-bs-target="#hpGaleri" data-bs-slide-to="<?php echo $i; ?>" <?php echo $i === 0 ? 'class="active" aria-current="true"' : ''; ?> aria-label="Foto <?php echo $i + 1; ?>"></button>
                <?php endforeach; ?>
              </div>

              <div class="carousel-inner">
                <?php foreach ($galeri as $i => $gambar): ?>
                  <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>">
                    <img src="<?php echo $home->item_media($gambar); ?>" alt="<?php echo htmlspecialchars($gambar['item_judul']); ?>">
                    <?php if ($gambar['item_judul'] !== ''): ?>
                      <div class="hp-profil__caption"><?php echo htmlspecialchars($gambar['item_judul']); ?></div>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              </div>

              <?php if (count($galeri) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#hpGaleri" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Sebelumnya</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#hpGaleri" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Berikutnya</span>
                </button>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ================= VISI & MISI ================= -->
  <?php if ($home->on('section_visi')): ?>
  <section class="hp-section" id="visi-misi">
    <div class="container">
      <div class="text-center mb-5" data-reveal>
        <span class="hp-eyebrow">Arah Mutu</span>
        <h2 class="hp-title"><?php echo htmlspecialchars($home->val('visi_judul', 'Visi & Misi')); ?></h2>
        <div class="hp-rule"></div>
      </div>

      <div class="row g-4">
        <div class="col-lg-5" data-reveal>
          <div class="hp-card hp-card--visi">
            <div class="hp-card__head">
              <i class="bi bi-eye"></i>
              <h3>Visi</h3>
            </div>
            <p class="hp-card__text text-secondary"><?php echo $home->val('visi_isi'); ?></p>
          </div>
        </div>

        <div class="col-lg-7" data-reveal>
          <div class="hp-card">
            <div class="hp-card__head hp-card__head--aksen">
              <i class="bi bi-list-check"></i>
              <h3>Misi</h3>
            </div>
            <ul class="hp-list hp-list--aksen">
              <?php foreach ($misi as $item): ?>
                <li><?php echo htmlspecialchars($item['item_isi']); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ================= TUPOKSI ================= -->
  <?php if ($home->on('section_tupoksi') && !empty($tupoksi)): ?>
  <section class="hp-section hp-section--soft" id="tupoksi">
    <div class="container">
      <div class="text-center mb-5" data-reveal>
        <span class="hp-eyebrow">Tugas & Fungsi</span>
        <h2 class="hp-title"><?php echo htmlspecialchars($home->val('tupoksi_judul', 'Tugas Pokok dan Fungsi')); ?></h2>
        <div class="hp-rule"></div>
      </div>

      <div class="hp-tupoksi">
        <?php $no = 1; foreach ($tupoksi as $item): ?>
          <div class="hp-tupoksi__item" data-reveal>
            <div class="hp-tupoksi__no"><?php echo $no++; ?></div>
            <p class="hp-tupoksi__text"><?php echo htmlspecialchars($item['item_isi']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ================= SASARAN MUTU ================= -->
  <?php if ($home->on('section_sasaran')): ?>
  <section class="hp-section" id="sasaran-mutu">
    <div class="container">
      <div class="text-center mb-5" data-reveal>
        <span class="hp-eyebrow">Target Mutu</span>
        <h2 class="hp-title"><?php echo htmlspecialchars($home->val('sasaran_judul', 'Sasaran Mutu')); ?></h2>
        <div class="hp-rule"></div>
      </div>

      <div class="row g-4">
        <div class="col-lg-5" data-reveal>
          <div class="hp-callout">
            <h3><i class="bi bi-bullseye me-2"></i><?php echo htmlspecialchars($home->val('sasaran_umum_judul', 'Sasaran Mutu - Umum')); ?></h3>
            <p><?php echo $home->val('sasaran_umum_isi'); ?></p>
          </div>
        </div>

        <div class="col-lg-7" data-reveal>
          <div class="hp-card">
            <div class="hp-card__head">
              <i class="bi bi-check2-square"></i>
              <h3><?php echo htmlspecialchars($home->val('sasaran_khusus_judul', 'Sasaran Mutu - Khusus')); ?></h3>
            </div>
            <ul class="hp-list">
              <?php foreach ($sasaran as $item): ?>
                <li><?php echo htmlspecialchars($item['item_isi']); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ================= PENGELOLA ================= -->
  <?php if ($home->on('section_pengelola')): ?>
  <section class="hp-section hp-section--soft" id="pengelola">
    <div class="container">
      <div class="text-center mb-5" data-reveal>
        <span class="hp-eyebrow">Tim</span>
        <h2 class="hp-title"><?php echo htmlspecialchars($home->val('pengelola_judul')); ?></h2>
        <p class="hp-subtitle"><?php echo htmlspecialchars($home->val('pengelola_deskripsi')); ?></p>
        <div class="hp-rule"></div>
      </div>

      <div class="row justify-content-center" data-reveal>
        <div class="col-lg-10">
          <figure class="hp-figure mb-0">
            <img src="<?php echo $home->media('pengelola_gambar'); ?>" alt="Pengelola <?php echo htmlspecialchars($institusi); ?>">
            <figcaption><?php echo htmlspecialchars($institusi); ?> - <?php echo htmlspecialchars($home->val('pengelola_judul')); ?></figcaption>
          </figure>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ================= STRUKTUR ORGANISASI ================= -->
  <?php if ($home->on('section_struktur')): ?>
  <section class="hp-section" id="struktur">
    <div class="container">
      <div class="text-center mb-5" data-reveal>
        <span class="hp-eyebrow">Organisasi</span>
        <h2 class="hp-title"><?php echo htmlspecialchars($home->val('struktur_judul')); ?></h2>
        <p class="hp-subtitle"><?php echo htmlspecialchars($home->val('struktur_deskripsi')); ?></p>
        <div class="hp-rule"></div>
      </div>

      <div class="row justify-content-center" data-reveal>
        <div class="col-lg-10">
          <figure class="hp-figure mb-0">
            <img src="<?php echo $home->media('struktur_gambar'); ?>" alt="Struktur Organisasi <?php echo htmlspecialchars($institusi); ?>">
          </figure>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ================= SURAT KEPUTUSAN ================= -->
  <?php if ($home->on('section_sk')): ?>
  <section class="hp-section hp-section--soft" id="sk">
    <div class="container">
      <div class="text-center mb-5" data-reveal>
        <span class="hp-eyebrow">Dokumen</span>
        <h2 class="hp-title"><?php echo htmlspecialchars($home->val('sk_judul', 'Surat Keputusan')); ?></h2>
        <p class="hp-subtitle"><?php echo htmlspecialchars($home->val('sk_deskripsi')); ?></p>
        <div class="hp-rule"></div>
      </div>

      <div class="row justify-content-center">
        <div class="col-lg-10" data-reveal>
          <?php if (!empty($sk)): ?>
            <div class="hp-table-wrap">
              <div class="table-responsive">
                <table class="hp-table">
                  <thead>
                    <tr>
                      <th width="70">No</th>
                      <th>Nama Dokumen</th>
                      <th width="170" class="text-center">Unduh</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 1; foreach ($sk as $row): ?>
                      <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($row['sk_judul']); ?></td>
                        <td class="text-center">
                          <a class="hp-dl" href="<?php echo base_url('filedata/') . rawurlencode($row['sk_file']); ?>" target="_blank" rel="noopener">
                            <i class="bi bi-download"></i> Unduh
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php else: ?>
            <div class="hp-empty">Belum ada dokumen surat keputusan yang dipublikasikan.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ================= BERITA ================= -->
  <?php if ($home->on('section_berita')): ?>
  <section class="hp-section" id="berita">
    <div class="container">
      <div class="text-center mb-5" data-reveal>
        <span class="hp-eyebrow">Informasi</span>
        <h2 class="hp-title"><?php echo htmlspecialchars($home->val('berita_judul', 'Berita Terbaru')); ?></h2>
        <p class="hp-subtitle"><?php echo htmlspecialchars($home->val('berita_deskripsi')); ?></p>
        <div class="hp-rule"></div>
      </div>

      <?php if (!empty($berita)): ?>
        <div class="row g-4">
          <?php foreach (array_slice($berita, 0, 6) as $row): ?>
            <?php $tanggal = hp_tanggal(isset($row['berita_create']) ? $row['berita_create'] : ''); ?>
            <div class="col-md-6 col-lg-4" data-reveal>
              <article class="hp-post">
                <div class="hp-post__thumb">
                  <?php if (!empty($row['berita_file'])): ?>
                    <img src="<?php echo base_url('filedata/') . rawurlencode($row['berita_file']); ?>" alt="<?php echo htmlspecialchars($row['berita_judul']); ?>">
                  <?php endif; ?>
                  <span class="hp-post__date"><i class="bi bi-calendar3"></i> <?php echo $tanggal['lengkap']; ?></span>
                </div>

                <div class="hp-post__body">
                  <h3 class="hp-post__title">
                    <a href="<?php echo base_url('berita?id=' . $row['berita_id']); ?>"><?php echo htmlspecialchars($row['berita_judul']); ?></a>
                  </h3>
                  <p class="hp-post__excerpt"><?php echo htmlspecialchars(hp_potong(isset($row['berita_deskripsi']) ? $row['berita_deskripsi'] : '', 22)); ?></p>
                  <a class="hp-post__link" href="<?php echo base_url('berita?id=' . $row['berita_id']); ?>">
                    Baca selengkapnya <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </article>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="hp-empty">Belum ada berita yang dipublikasikan.</div>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- ================= PENGUMUMAN ================= -->
  <?php if ($home->on('section_pengumuman')): ?>
  <section class="hp-section hp-section--soft" id="pengumuman">
    <div class="container">
      <div class="text-center mb-5" data-reveal>
        <span class="hp-eyebrow">Pengumuman</span>
        <h2 class="hp-title"><?php echo htmlspecialchars($home->val('pengumuman_judul', 'Pengumuman')); ?></h2>
        <p class="hp-subtitle"><?php echo htmlspecialchars($home->val('pengumuman_deskripsi')); ?></p>
        <div class="hp-rule"></div>
      </div>

      <?php if (!empty($pengumuman)): ?>
        <div class="row g-4">
          <?php foreach (array_slice($pengumuman, 0, 6) as $row): ?>
            <?php $tanggal = hp_tanggal(isset($row['pengumuman_create']) ? $row['pengumuman_create'] : ''); ?>
            <div class="col-md-6" data-reveal>
              <div class="hp-announce">
                <div class="hp-announce__date">
                  <span><strong><?php echo $tanggal['hari']; ?></strong><?php echo $tanggal['bulan']; ?><br><?php echo $tanggal['tahun']; ?></span>
                </div>
                <div>
                  <h3 class="hp-announce__title">
                    <a href="<?php echo base_url('pengumuman?id=' . $row['pengumuman_id']); ?>"><?php echo htmlspecialchars($row['pengumuman_judul']); ?></a>
                  </h3>
                  <p class="hp-announce__text"><?php echo htmlspecialchars(hp_potong(isset($row['pengumuman_deskripsi']) ? $row['pengumuman_deskripsi'] : '', 18)); ?></p>
                  <a class="hp-post__link" href="<?php echo base_url('pengumuman?id=' . $row['pengumuman_id']); ?>">
                    Baca selengkapnya <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="hp-empty">Belum ada pengumuman yang dipublikasikan.</div>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- ================= KONTAK ================= -->
  <?php if ($home->on('section_kontak')): ?>
  <section class="hp-section" id="kontak">
    <div class="container">
      <div class="text-center mb-5" data-reveal>
        <span class="hp-eyebrow">Kontak</span>
        <h2 class="hp-title"><?php echo htmlspecialchars($home->val('kontak_judul', 'Hubungi Kami')); ?></h2>
        <div class="hp-rule"></div>
      </div>

      <div class="row g-4">
        <div class="col-lg-5" data-reveal>
          <div class="row g-3">
            <div class="col-12">
              <div class="hp-kontak__item">
                <i class="bi bi-geo-alt"></i>
                <div>
                  <h4>Alamat</h4>
                  <p><?php echo nl2br(htmlspecialchars($home->val('kontak_alamat'))); ?></p>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="hp-kontak__item">
                <i class="bi bi-telephone"></i>
                <div>
                  <h4>Telepon</h4>
                  <p><?php echo htmlspecialchars($home->val('kontak_telepon')); ?></p>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="hp-kontak__item">
                <i class="bi bi-envelope"></i>
                <div>
                  <h4>Email</h4>
                  <p><a href="mailto:<?php echo htmlspecialchars($home->val('kontak_email')); ?>"><?php echo htmlspecialchars($home->val('kontak_email')); ?></a></p>
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="hp-kontak__item">
                <i class="bi bi-globe2"></i>
                <div>
                  <h4>Website</h4>
                  <p><a href="<?php echo htmlspecialchars($home->val('kontak_website')); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($home->val('kontak_website')); ?></a></p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7" data-reveal>
          <?php if ($home->val('kontak_maps') !== ''): ?>
            <div class="hp-maps"><?php echo $home->val('kontak_maps'); ?></div>
          <?php else: ?>
            <figure class="hp-figure h-100 d-flex flex-column justify-content-center mb-0 text-center">
              <img src="<?php echo $home->media('site_logo', base_url('assets/assets/images/logostik.png')); ?>" alt="<?php echo htmlspecialchars($institusi); ?>" style="max-width: 210px; margin: 0 auto;">
              <figcaption class="mt-4">
                <strong class="d-block hp-heading fs-5 mb-2"><?php echo htmlspecialchars($institusi); ?></strong>
                <span><?php echo htmlspecialchars($home->val('kontak_alamat')); ?></span>
              </figcaption>
            </figure>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

</main>

<!-- ================= FOOTER ================= -->
<footer class="hp-footer">
  <div class="container">
    <div class="row g-4">

      <div class="col-lg-5">
        <div class="hp-footer__brand">
          <img src="<?php echo $home->media('site_logo', base_url('assets/assets/images/logostik.png')); ?>" alt="Logo <?php echo htmlspecialchars($institusi); ?>">
          <span>
            <strong><?php echo htmlspecialchars($nama); ?></strong>
            <span><?php echo htmlspecialchars($home->val('site_subjudul', '')); ?></span>
          </span>
        </div>
        <p><?php echo htmlspecialchars(hp_potong($home->val('hero_deskripsi', ''), 26)); ?></p>
        <div class="hp-footer__social">
          <a href="mailto:<?php echo htmlspecialchars($home->val('kontak_email')); ?>" title="Email"><i class="bi bi-envelope"></i></a>
          <a href="<?php echo htmlspecialchars($home->val('kontak_website')); ?>" target="_blank" rel="noopener" title="Website"><i class="bi bi-globe2"></i></a>
          <a href="<?php echo base_url('login'); ?>" title="Login"><i class="bi bi-box-arrow-in-right"></i></a>
        </div>
      </div>

      <div class="col-6 col-lg-2">
        <h4>Profil</h4>
        <ul class="hp-footer__links">
          <?php foreach ($menu_section as $menu): ?>
            <?php if ($menu['id'] === 'beranda' || !$menu['tampil']) { continue; } ?>
            <li><a href="#<?php echo $menu['id']; ?>"><?php echo htmlspecialchars($menu['label']); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="col-6 col-lg-2">
        <h4>SPMI</h4>
        <ul class="hp-footer__links">
          <li><a href="<?php echo base_url('penetapan'); ?>">Penetapan</a></li>
          <li><a href="<?php echo base_url('pelaksanaan'); ?>">Pelaksanaan</a></li>
          <li><a href="<?php echo base_url('evaluasi'); ?>">Evaluasi</a></li>
          <li><a href="<?php echo base_url('pengendalian'); ?>">Pengendalian</a></li>
          <li><a href="<?php echo base_url('peningkatan'); ?>">Peningkatan</a></li>
        </ul>
      </div>

      <div class="col-lg-3">
        <h4>Kontak</h4>
        <ul class="hp-footer__links">
          <li><i class="bi bi-geo-alt me-2"></i><?php echo htmlspecialchars(hp_potong($home->val('kontak_alamat'), 16)); ?></li>
          <li><i class="bi bi-telephone me-2"></i><?php echo htmlspecialchars($home->val('kontak_telepon')); ?></li>
          <li><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($home->val('kontak_email')); ?></li>
        </ul>
      </div>

    </div>

    <div class="hp-footer__bottom">
      <span>
        <?php echo htmlspecialchars($home->val('footer_teks')); ?>
        <a href="<?php echo htmlspecialchars($home->val('kontak_website')); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($institusi); ?></a>
      </span>
      <span>&copy; <span data-tahun><?php echo date('Y'); ?></span> <?php echo htmlspecialchars($home->val('footer_kredit')); ?></span>
    </div>
  </div>
</footer>

<button class="hp-top" id="hpTop" type="button" title="Ke atas" aria-label="Kembali ke atas">
  <i class="bi bi-arrow-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo asset_url(); ?>desain/home.js?v=1.0.0"></script>
</body>
</html>
