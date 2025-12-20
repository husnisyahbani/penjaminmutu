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

  </style>
</head>
<body>

<!-- <header class="main-header fixed-top">
  <nav class="navbar navbar-expand-lg bg-white shadow-lg rounded-5 px-lg-4 mx-lg-5 mt-3 mt-lg-5">
    <div class="container-fluid">
      <a class="navbar-brand px-0" href="<?php echo base_url();?>">
        <img src="<?php echo asset_url();?>/assets/images/logostik.png" alt="STIK SITI KHADIJAH Logo" height="80">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama" aria-controls="menuUtama" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="menuUtama">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link py-3 px-4 active" aria-current="page" href="<?php echo base_url();?>">Beranda</a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-3 px-4" href="<?php echo base_url();?>#fitur">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-3 px-4" href="<?php echo base_url();?>#kontak">Kontak</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header> -->

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

          <!-- INFORMASI PUBLIK -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle py-3 px-4" 
               href="#" 
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
                   href="<?php echo base_url('publik/berita'); ?>">
                  Berita
                </a>
              </li>

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url('publik/pengumuman'); ?>">
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
                   href="<?php echo base_url('tentang/profil'); ?>">
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
                   href="<?php echo base_url('tentang/struktur'); ?>">
                  Visi & Misi & Sasaran Mutu
                </a>
              </li>

              <li>
                <a class="dropdown-item py-2 px-4"
                   href="<?php echo base_url('tentang/struktur'); ?>">
                  Program Kerja
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


<section id="hero" class="py-5 bg-primary green-gradasi">
  <div class="container py-5 mt-4">
    <div class="row pt-5 justify-content-center align-items-center">
      <div class="col-lg-5">
        <img width="400px" srcset="<?php echo base_url("/assets/assets/images/sijamuhomepage.png");?> 2x" src="<?php echo base_url("/assets/assets/images/sijamuhomepage.png");?>" alt="SIJAMU" class="img-fluid mt-5">
      </div>
      <div class="col-lg-7">
        <h1 class="text-lg-end mt-5 mb-5" style="font-weight: bold; color: black;">
          <strong>SIJAMU 
            <span style="display: block; margin-top: 3px; font-weight: bold; color: black; font-size: 20px;">
              Sistem Informasi Penjamin Mutu
            </span>
          </strong>
        </h1>
        <p class="text-lg-end mt-5 mb-5" style="font-weight: bold; color: black;">
          Selamat Datang di SIJAMU, Media Informasi, Monitoring, dan evaluasi mutu untuk mendukung pelaksanaan Sistem Penjamin Mutu Internal STIK SITI KHADIJAH secara terintegrasi, transparan, dan berkelanjutan dalam mewujudkan pendidikan tinggi yang bermutu
        </p>
        <p class="text-lg-end">
          <a class="btn btn-warning btn-lg px-5 py-3 rounded-5" href="<?php echo base_url();?>#fitur" role="button">Pelajari lebih lanjut</a>
        </p>
      </div>
    </div>
  </div>
</section>

<main id="konten" class="pb-5 position-relative">
  <section id="fitur" class="py-5">
    <div class="container py-5">
      <div class="row py-5">
        <div class="col-lg-4 d-flex flex-column justify-content-start mb-5 text-center">
          <img srcset="./assets/desain/img-kep.png 2x" src="./assets/desain/img-kep.png" alt="LPM" class="mb-3">
          <h1 class="mb-2">PPM</h1>
          <p class="mb-3">Pusat Penjamin Mutu</p>
          <br/>
          <p>
            <a href="<?php echo base_url("login");?>" class="mt-3 px-5 rounded-5 btn btn-lg green-gradasi text-white align-self-baseline">Login sebagai PPM</a>
          </p>
        </div>
        <div class="col-lg-4 d-flex flex-column justify-content-start mb-5 text-center">
          <img srcset="./assets/desain/img-penelaah.png 2x" src="./assets/desain/img-penelaah.png" alt="auditor" class="mb-3">
          <h1 class="mb-2">Auditor</h1>
          <p class="mb-3">Anggota PPM untuk melakukan audit protokol yang masuk</p>
          <p><a href="<?php echo base_url("login");?>" class="mt-3 px-5 rounded-5 btn btn-lg yellow-gradasi text-white align-self-baseline">Login sebagai Auditor</a></p>
        </div>
        <div class="col-lg-4 d-flex flex-column justify-content-start mb-5 text-center">
          <img srcset="./assets/desain/img-pengusul.png 2x" src="./assets/desain/img-pengusul.png" alt="auditee" class="mb-3">
          <h1 class="mb-2">Auditee</h1>
          <p class="mb-3">Anggota PPM yang dilakukan audit</p>
          <br/>
          <p>
            <a href="<?php echo base_url("login");?>" class="mt-3 px-5 rounded-5 btn btn-lg red-gradasi text-white align-self-baseline">Login sebagai Auditee</a>
          </p>
        </div>
      </div>
    </div>
  </section>

  <section id="sk" class="py-5">
    <div class="container py-5">
      <div class="row py-5 justify-content-center align-items-center">
        <div class="col-12">
          <h1 class="text-center my-5">Surat Keputusan</h1>
        </div>
        <div class="col-12 align-center">
          <table style="
    width:80%;
    margin:30px auto;
    border-collapse:collapse;
    font-family:'Segoe UI', Tahoma, sans-serif;
    background:#ffffff;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
    border-radius:10px;
    overflow:hidden;
">
    <thead>
        <tr style="background:#2c7be5; color:#ffffff;">
            <th style="padding:12px; text-align:center;">No</th>
            <th style="padding:12px; text-align:left;">Nama</th>
            <th style="padding:12px; text-align:center;">Download</th>
        </tr>
    </thead>
    <tbody>
        <tr style="border-bottom:1px solid #eaeaea;">
            <td style="padding:12px; text-align:center;">1</td>
            <td style="padding:12px;">Surat Keputusan Pembentukan LPM STIK SITI KHADIJAH</td>
            <td style="padding:12px; text-align:center;">
                <a href="<?php echo asset_url();?>/assets/images/SK_PEMBENTUKAN.pdf" style="
                    background:#28a745;
                    color:#ffffff;
                    padding:8px 14px;
                    text-decoration:none;
                    border-radius:6px;
                    font-size:14px;
                    display:inline-block;
                ">Download</a>
            </td>
        </tr>
        <tr style="background:#f9fbfd; border-bottom:1px solid #eaeaea;">
            <td style="padding:12px; text-align:center;">2</td>
            <td style="padding:12px;">Surat Keputusan Pengangkatan Tim Gugus Kendali Mutu</td>
            <td style="padding:12px; text-align:center;">
                <a href="<?php echo asset_url();?>/assets/images/SK_GKM.pdf" style="
                    background:#17a2b8;
                    color:#ffffff;
                    padding:8px 14px;
                    text-decoration:none;
                    border-radius:6px;
                    font-size:14px;
                    display:inline-block;
                ">Download</a>
            </td>
        </tr>
        <tr style="border-bottom:1px solid #eaeaea;">
            <td style="padding:12px; text-align:center;">3</td>
            <td style="padding:12px;">Surat Keputusan Perubahan Nama</td>
            <td style="padding:12px; text-align:center;">
                <a href="<?php echo asset_url();?>/assets/images/SK_PPM.pdf" style="
                    background:#ffc107;
                    color:#212529;
                    padding:8px 14px;
                    text-decoration:none;
                    border-radius:6px;
                    font-size:14px;
                    display:inline-block;
                ">Download</a>
            </td>
        </tr>
        <tr>
            <td style="padding:12px; text-align:center;">4</td>
            <td style="padding:12px;">Surat Keputusan Pengangkatan Tim Auditor Internal</td>
            <td style="padding:12px; text-align:center;">
                <a href="<?php echo asset_url();?>/assets/images/SK_AUDITOR.pdf" style="
                    background:#dc3545;
                    color:#ffffff;
                    padding:8px 14px;
                    text-decoration:none;
                    border-radius:6px;
                    font-size:14px;
                    display:inline-block;
                ">Download</a>
            </td>
        </tr>
    </tbody>
</table>


        </div>
        <div class="col-md-8 text-center">
          
        </div>
      </div>
    </div>
  </section>

  <section id="struktur" class="py-5">
    <div class="container py-5">
      <div class="row py-5 justify-content-center align-items-center">
        <div class="col-12">
          <h1 class="text-center my-5">Struktur Organisasi</h1>
        </div>
        <div class="col-12 text-center">
          <img srcset="<?php echo asset_url();?>/assets/images/strukturorganisasi_fix.png 2x" src="<?php echo asset_url();?>/assets/images/strukturorganisasi_fix.png" alt="PENJAMIN MUTU STIK SITI KHADIJAH" class="img-fluid mb-4">
        </div>
        <div class="col-md-8 text-center">
          
        </div>
      </div>
    </div>
  </section>

  <section id="kontak" class="py-5">
    <div class="container py-5">
      <div class="row py-5 justify-content-center align-items-center">
        <div class="col-12">
          <h1 class="text-center my-5">Kontak</h1>
        </div>
        <div class="col-12 text-center">
          <img srcset="<?php echo asset_url();?>/assets/images/logostik.png 2x" src="<?php echo asset_url();?>/assets/images/logostik.png" alt="STIK SITI KHADIJAH" class="img-fluid mb-4">
        </div>
        <div class="col-md-8 text-center">
          <h3 class="mb-4">STIK SITI KHADIJAH</h3>
          <p>
            JL. Demang Lebar Daun Kelurahan Lorok Pakjo Kecamatan Ilir Barat 1 Kota Palembang Provinsi Sumatera Selatan Kode Pos : 30137 <br>
            (0711) 315010 <br>
            spmi@stik-sitikhadijah.ac.id <br>
            <a href="https://stik-sitikhadijah.ac.id">stik-sitikhadijah.ac.id</a>
          </p>
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

<!-- Scroll & Navbar Control -->
<script>
function animateScroll() {
  window.scroll({ top: 0, behavior: 'smooth' });
}

// Navbar hide/show saat scroll
let lastScrollTop = 0;
const navbar = document.querySelector('.main-header');

// window.addEventListener('scroll', function() {
//   let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  
//   if (scrollTop > lastScrollTop && scrollTop > 100) {
//     navbar.style.top = "-200px";
//   } else {
//     navbar.style.top = "0";
//   }
//   lastScrollTop = scrollTop;
// });

// Tutup navbar otomatis setelah klik link
const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
const navbarCollapse = document.querySelector('.navbar-collapse');
navLinks.forEach(link => {
  link.addEventListener('click', () => {
    const bsCollapse = new bootstrap.Collapse(navbarCollapse, { toggle: false });
    bsCollapse.hide();
  });
});
</script>

</body>
</html>
