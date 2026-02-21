<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SIJAMU</title>
  <meta name="description" content="SIJAMU, sistem penjamin mutu di stik siti khadijah">
  <meta name="keyword" content="Sistem audit stik siti khadijah">
  <meta name="author" content="STIK SITI KHADIJAH">
  <link href="<?php echo base_url("/assets/assets/images/logostik.png");?>" rel="icon" type="image/x-icon">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS Landing Page -->
  <link rel="stylesheet" href="./assets/desain/landing.css">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com/">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
  <link href="./assets/desain/css2" rel="stylesheet"> 

  <!-- Google tag (gtag.js) -->
  <script async src="./assets/desain/js"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-S75WL5XL9T');
  </script>

  <style>
    .main-header {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 999;
      transition: top 0.3s;
    }

    /* =========================
   NAVBAR HOVER HIJAU
   ========================= */

      /* Menu utama */
      .navbar .nav-link {
        transition: all 0.3s ease;
      }

      .navbar .nav-link:hover,
      .navbar .nav-link:focus {
        color: #198754 !important; /* hijau bootstrap */
      }

      /* Dropdown toggle hover */
      .navbar .dropdown-toggle:hover {
        color: #198754 !important;
      }

      /* Dropdown item */
      .dropdown-menu .dropdown-item {
        transition: all 0.2s ease;
      }

      .dropdown-menu .dropdown-item:hover,
      .dropdown-menu .dropdown-item:focus {
        background-color: #e9f7ef; /* hijau lembut */
        color: #198754;
      }

      /* Aktif menu */
      .navbar .nav-link.active {
        color: #198754 !important;
        font-weight: 600;
      }

      .table-wrapper {
        background: #ffffff;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,.04);
    }

    .table thead th {
        background-color: #f8fafc;
        font-weight: 600;
        color: #334155;
        border-bottom: 1px solid #e5e7eb;
    }

    .table tbody td {
        vertical-align: middle;
        color: #475569;
    }

    .table tbody tr:hover {
        background-color: #f1f5f9;
    }

    .file-badge {
        background: #eef2ff;
        color: #4338ca;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
    }

    .main-header {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 999;
    }

  </style>
</head>
<body>

<header class="main-header fixed-top">
  <nav class="navbar navbar-expand-lg bg-white shadow-lg rounded-5 px-lg-4 mx-lg-5 mt-3 mt-lg-5">
    <div class="container-fluid">

      <!-- LOGO -->
      <a class="navbar-brand px-0" href="<?php echo base_url();?>">
        <img src="<?php echo asset_url();?>/assets/images/logostik.png" 
             alt="STIK SITI KHADIJAH Logo" height="80">
      </a>

      <!-- TOGGLER -->
      <button class="navbar-toggler" type="button" 
              data-bs-toggle="collapse" 
              data-bs-target="#menuUtama"
              aria-controls="menuUtama" 
              aria-expanded="false" 
              aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- MENU -->
      <div class="collapse navbar-collapse" id="menuUtama">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

          <!-- BERANDA -->
          <li class="nav-item">
            <a class="nav-link py-3 px-4 active" 
               aria-current="page" 
               href="<?php echo base_url();?>">
              Beranda
            </a>
          </li>

          <!-- SPMI -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle py-3 px-4" 
               href="javascript:void(0)" 
               id="dropdownSPMI" 
               role="button" 
               data-bs-toggle="dropdown" 
               aria-expanded="false">
              SPMI
            </a>

            <ul class="dropdown-menu shadow rounded-4 border-0"
                aria-labelledby="dropdownSPMI">

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url('penetapan'); ?>">
                  Penetapan
                </a>
              </li>

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url('pelaksanaan'); ?>">
                  Pelaksanaan
                </a>
              </li>

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url('evaluasi'); ?>">
                  Evaluasi
                </a>
              </li>

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url('pengendalian'); ?>">
                  Pengendalian
                </a>
              </li>

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url('peningkatan'); ?>">
                  Peningkatan
                </a>
              </li>

              

            </ul>
          </li>

          <!-- INFORMASI PUBLIK -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle py-3 px-4" 
               href="javascript:void(0)" 
               id="dropdownInformasiPublik" 
               role="button" 
               data-bs-toggle="dropdown" 
               aria-expanded="false">
              Informasi Publik
            </a>

            <ul class="dropdown-menu shadow rounded-4 border-0"
                aria-labelledby="dropdownInformasiPublik">

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url();?>#berita">
                  Berita
                </a>
              </li>

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url(); ?>#pengumuman">
                  Pengumuman
                </a>
              </li>

              

            </ul>
          </li>

          <!-- DROPDOWN TENTANG KAMI -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle py-3 px-4" 
               href="#" 
               id="dropdownTentangKami" 
               role="button" 
               data-bs-toggle="dropdown" 
               aria-expanded="false">
              Tentang Kami
            </a>

            <ul class="dropdown-menu shadow rounded-4 border-0"
                aria-labelledby="dropdownTentangKami">

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url(); ?>#profil">
                  Profil
                </a>
              </li>

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url(); ?>#sk">
                  SK
                </a>
              </li>

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url(); ?>#struktur">
                  Struktur Organisasi
                </a>
              </li>

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url(); ?>#visi-misi">
                  Visi & Misi
                </a>
              </li>

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url(); ?>#tupoksi">
                  Tupoksi
                </a>
              </li>

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url(); ?>#sasaran-mutu">
                  Sasaran Mutu
                </a>
              </li>

            </ul>
          </li>

          <!-- LOGIN -->
          <li class="nav-item">
            <a class="nav-link py-3 px-4" 
               href="<?php echo base_url();?>#fitur">
              Login
            </a>
          </li>

          <!-- KONTAK -->
          <li class="nav-item">
            <a class="nav-link py-3 px-4" 
               href="<?php echo base_url();?>#kontak">
              Kontak
            </a>
          </li>

        </ul>
      </div>

    </div>
  </nav>
</header>

<section class="py-5 bg-primary green-gradasi">
    <div class="container my-5">
       <h1 class="text-center my-5">Sasaran Mutu</h1>
    <div class="table-wrapper">

        <h5 class="mb-4 fw-semibold text-dark">
            Daftar Dokumen Penjaminan Mutu
        </h5>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">No</th>
                        <th>Uraian</th>
                        <th>Keterangan</th>
                        <th style="width:140px">File</th>
                        <th style="width:140px">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                  <?php foreach($dataku as $index => $out) { ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $out['data_uraian'] ?></td>
                        <td><?= $out['data_keterangan'] ?></td>
                        <td>
                          <a class="btn btn-sm btn-icon btn-success"
                              data-toggle="tooltip" data-original-title="Detail" href="<?php echo base_url('filedata/'.$out['data_file']); ?>" target="_blank"><i class="icon md-download" aria-hidden="true"></i> Download</a>
                        </td>
                        <td><?= date("d-m-Y H:i:s", strtotime($out['data_create'])) ?></td>
                    </tr>
                  <?php } ?>

                    

                    
                </tbody>
            </table>
        </div>

    </div>
</div>
</section>



 <footer class="shadow-lg rounded-5 my-5 mx-5">
    <div class="container py-3">
      <div class="row">
        <div class="col-12">
          <p class="text-center m-0">Dikembangkan oleh <a href="https://stik-sitikhadijah.ac.id">STIK SITI KHADIJAH</a></p>
        </div>
      </div>
    </div>
  </footer>

  <button class="btn btn-sm btn-outline-dark px-3 position-absolute" title="Scroll to Top" onclick="animateScroll()" style="bottom:15px; right:15px;z-index:3;">⤒</button>
</main>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function animateScroll() {
  window.scroll({ top: 0, behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', function () {

  const navbarCollapse = document.querySelector('.navbar-collapse');

  // Tangani SEMUA klik di dalam navbar
  document.querySelector('.navbar-nav')
    .addEventListener('click', function (e) {

      const target = e.target.closest('a');
      if (!target) return;

      const href = target.getAttribute('href');

      // ❌ Jangan tutup navbar jika:
      if (
        target.classList.contains('dropdown-toggle') ||   // klik item dropdown
        href === '#' ||
        href === 'javascript:void(0)'
      ) {
        return;
      }

      // ✅ Tutup navbar HANYA untuk link navigasi nyata
      const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
        toggle: false
      });
      bsCollapse.hide();
    });

});
</script>



</body>
</html>