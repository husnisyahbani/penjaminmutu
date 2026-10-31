<?php
/**
 * Shell atas halaman turunan: login & sub halaman SPMI.
 *
 * Memakai tema yang sama dengan homepage (Bootstrap 5, Plus Jakarta Sans,
 * assets/desain/home.css + assets/desain/halaman.css) sehingga navbar,
 * warna, font, dan footer tampil konsisten dengan halaman depan.
 *
 * Variabel masukan:
 *   $title      judul halaman                       (wajib)
 *   $page       penanda halaman aktif: '' | 'login' | 'spmi'
 *   $home       HomeModel                           (opsional, dimuat otomatis)
 *   $head_extra tambahan HTML mentah pada <head>    (opsional)
 */

$this->load->helper('hp');

if (!isset($home)) {
    $this->load->model('HomeModel', 'home');
    $home = $this->home;
}

$warna_primer = $home->val('warna_primer', '#0f766e');
$warna_aksen  = $home->val('warna_aksen', '#f59e0b');
$institusi    = $home->val('site_institusi', 'STIK Siti Khadijah');
$nama         = $home->val('site_nama', 'SIJAMU');
$subjudul     = $home->val('site_subjudul', '');
$logo         = $home->media('site_logo', base_url('assets/assets/images/logostik.png'));
$halaman      = isset($page) ? $page : '';

$menu_section = array(
    array('id' => 'profil', 'label' => 'Profil', 'tampil' => $home->on('section_profil')),
    array('id' => 'visi-misi', 'label' => 'Visi & Misi', 'tampil' => $home->on('section_visi')),
    array('id' => 'tupoksi', 'label' => 'Tupoksi', 'tampil' => $home->on('section_tupoksi')),
    array('id' => 'sasaran-mutu', 'label' => 'Sasaran Mutu', 'tampil' => $home->on('section_sasaran')),
    array('id' => 'pengelola', 'label' => 'Pengelola', 'tampil' => $home->on('section_pengelola')),
    array('id' => 'struktur', 'label' => 'Struktur Organisasi', 'tampil' => $home->on('section_struktur')),
    array('id' => 'sk', 'label' => 'Surat Keputusan', 'tampil' => $home->on('section_sk')),
);

$ada_profil = FALSE;
foreach ($menu_section as $menu) {
    if ($menu['tampil']) {
        $ada_profil = TRUE;
        break;
    }
}

$spmi_menu = array(
    array('slug' => 'penetapan', 'label' => 'Penetapan'),
    array('slug' => 'pelaksanaan', 'label' => 'Pelaksanaan'),
    array('slug' => 'evaluasi', 'label' => 'Evaluasi'),
    array('slug' => 'pengendalian', 'label' => 'Pengendalian'),
    array('slug' => 'peningkatan', 'label' => 'Peningkatan'),
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php echo htmlspecialchars($title); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars(hp_potong($home->val('hero_deskripsi', ''), 30)); ?>">
  <meta name="keywords" content="SPMI, penjaminan mutu, audit mutu internal, <?php echo htmlspecialchars($institusi); ?>">
  <meta name="author" content="<?php echo htmlspecialchars($institusi); ?>">
  <link rel="icon" type="image/png" href="<?php echo $logo; ?>">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Gaya halaman depan + halaman turunan -->
  <link rel="stylesheet" href="<?php echo asset_url(); ?>desain/home.css?v=1.0.0">
  <link rel="stylesheet" href="<?php echo asset_url(); ?>desain/halaman.css?v=1.0.0">

  <style>
    :root {
      --hp-primer: <?php echo htmlspecialchars($warna_primer); ?>;
      --hp-primer-dark: <?php echo htmlspecialchars(hp_shade($warna_primer, -0.28)); ?>;
      --hp-primer-deep: <?php echo htmlspecialchars(hp_shade($warna_primer, -0.55)); ?>;
      --hp-primer-light: <?php echo htmlspecialchars(hp_shade($warna_primer, 0.25)); ?>;
      --hp-aksen: <?php echo htmlspecialchars($warna_aksen); ?>;
    }
  </style>
  <?php if (!empty($head_extra)) { echo $head_extra; } ?>
</head>
<body>

<!-- ================= NAVBAR ================= -->
<header class="hp-nav" id="hpNav">
  <nav class="navbar navbar-expand-lg">
    <div class="container">

      <a class="hp-nav__brand" href="<?php echo base_url(); ?>">
        <img src="<?php echo $logo; ?>" alt="Logo <?php echo htmlspecialchars($institusi); ?>">
        <span class="hp-nav__brand-text">
          <strong><?php echo htmlspecialchars($nama); ?></strong>
          <span><?php echo htmlspecialchars($subjudul); ?></span>
        </span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#hpMenu" aria-controls="hpMenu" aria-expanded="false" aria-label="Buka menu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="hpMenu">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item">
            <a class="nav-link hp-nav__link<?php echo $halaman === '' ? ' active' : ''; ?>" href="<?php echo base_url(); ?>">Beranda</a>
          </li>

          <?php if ($ada_profil): ?>
          <li class="nav-item dropdown">
            <a class="nav-link hp-nav__link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Tentang Kami
            </a>
            <ul class="dropdown-menu">
              <?php foreach ($menu_section as $menu): ?>
                <?php if (!$menu['tampil']) { continue; } ?>
                <li><a class="dropdown-item" href="<?php echo base_url() . '#' . $menu['id']; ?>"><?php echo htmlspecialchars($menu['label']); ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>
          <?php endif; ?>

          <li class="nav-item dropdown">
            <a class="nav-link hp-nav__link dropdown-toggle<?php echo $halaman === 'spmi' ? ' active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              SPMI
            </a>
            <ul class="dropdown-menu">
              <?php foreach ($spmi_menu as $menu): ?>
                <li><a class="dropdown-item" href="<?php echo base_url($menu['slug']); ?>"><?php echo htmlspecialchars($menu['label']); ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>

          <?php if ($home->on('section_berita') || $home->on('section_pengumuman')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link hp-nav__link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Informasi
            </a>
            <ul class="dropdown-menu">
              <?php if ($home->on('section_berita')): ?>
                <li><a class="dropdown-item" href="<?php echo base_url() . '#berita'; ?>"><?php echo htmlspecialchars($home->val('berita_judul', 'Berita')); ?></a></li>
              <?php endif; ?>
              <?php if ($home->on('section_pengumuman')): ?>
                <li><a class="dropdown-item" href="<?php echo base_url() . '#pengumuman'; ?>"><?php echo htmlspecialchars($home->val('pengumuman_judul', 'Pengumuman')); ?></a></li>
              <?php endif; ?>
            </ul>
          </li>
          <?php endif; ?>

          <?php if ($home->on('section_kontak')): ?>
          <li class="nav-item">
            <a class="nav-link hp-nav__link" href="<?php echo base_url() . '#kontak'; ?>">Kontak</a>
          </li>
          <?php endif; ?>

          <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
            <a class="hp-btn hp-btn--primer hp-nav__cta<?php echo $halaman === 'login' ? ' active' : ''; ?>" href="<?php echo base_url('login'); ?>">
              <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
          </li>
        </ul>
      </div>

    </div>
  </nav>
</header>

<main id="konten">
