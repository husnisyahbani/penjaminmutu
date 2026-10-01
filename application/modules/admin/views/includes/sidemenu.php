<div class="site-menubar">
      <ul class="site-menu">

        <?php /* Dashboard - paling atas, ringkasan statistik penjaminan mutu */ ?>
        <li class="site-menu-item <?php if(isset($dashboard)){echo $dashboard;} ?>">
          <a class="animsition-link" href="<?php echo base_url($module.'/dashboard');?>">
            <i class="site-menu-icon md-view-dashboard" aria-hidden="true"></i>
            <span class="site-menu-title">Dashboard</span>
          </a>
        </li>

        <li class="site-menu-item has-sub <?php if(isset($data)){echo $data;} ?>">
          <a href="javascript:void(0)">
                  <i class="site-menu-icon md-library" aria-hidden="true"></i>
                  <span class="site-menu-title">Dokument Mutu</span>
                          <span class="site-menu-arrow"></span>
         </a>

         <ul class="site-menu-sub">
           <li class="site-menu-item <?php if(isset($semua)){echo $semua;} ?>">
             <a class="animsition-link" href="<?php echo base_url($module.'/data');?>">
               <i class="icon md-view-list" aria-hidden="true"></i>
               <span class="site-menu-title">Semua</span>
             </a>
          </li>
          
        
           <li class="site-menu-item <?php if(isset($penetapan)){echo $penetapan;} ?>">
             <a class="animsition-link" href="<?php echo base_url($module.'/penetapan');?>">
               <i class="icon md-file-plus" aria-hidden="true"></i>
               <span class="site-menu-title">Penetapan</span>
             </a>
          </li>
            
            <li class="site-menu-item <?php if(isset($pelaksanaan)){echo $pelaksanaan;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/pelaksanaan');?>">
                <i class="icon md-play-circle" aria-hidden="true"></i>
                <span class="site-menu-title">Pelaksanaan</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($evaluasi)){echo $evaluasi;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/evaluasi');?>">
                <i class="icon md-chart" aria-hidden="true"></i>
                <span class="site-menu-title">Evaluasi</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($pengendalian)){echo $pengendalian;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/pengendalian');?>">
                <i class="icon md-shield-check" aria-hidden="true"></i>
                <span class="site-menu-title">Pengendalian</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($peningkatan)){echo $peningkatan;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/peningkatan');?>">
                <i class="icon md-trending-up" aria-hidden="true"></i>
                <span class="site-menu-title">Peningkatan</span>
              </a>
            </li>
            
         </ul>
        </li>

        
        
        

        <li class="site-menu-item has-sub <?php if(isset($auditmenu)){echo $auditmenu;} ?>">
          <a href="javascript:void(0)">
                  <i class="site-menu-icon md-assignment-check" aria-hidden="true"></i>
                  <span class="site-menu-title">Audit</span>
                          <span class="site-menu-arrow"></span>
         </a>
          
         <ul class="site-menu-sub">
           <li class="site-menu-item <?php if(isset($formaudit)){echo $formaudit;} ?>">
             <a class="animsition-link" href="<?php echo base_url($module.'/formaudit');?>">
               <i class="icon md-assignment" aria-hidden="true"></i>
               <span class="site-menu-title">Formulir Audit</span>
             </a>
        </li>
            
            <li class="site-menu-item <?php if(isset($audit)){echo $audit;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/daftaraudit');?>">
                <i class="icon md-format-list-bulleted" aria-hidden="true"></i>
                <span class="site-menu-title">Daftar Audit</span>
              </a>
            </li>
            
         </ul>
        </li>


        <!-- ==================== KONTEN ==================== -->
        <?php /* $website sudah berisi 'active' bila halaman dibuka dari menu ini */ ?>
        <li class="site-menu-item has-sub <?php if(isset($website)){echo $website;} ?>">
          <a href="javascript:void(0)">
                  <i class="site-menu-icon md-view-web" aria-hidden="true"></i>
                  <span class="site-menu-title">Konten</span>
                          <span class="site-menu-arrow"></span>
         </a>

         <ul class="site-menu-sub">
           <li class="site-menu-item <?php if(isset($berita)){echo $berita;} ?>">
             <a class="animsition-link" href="<?php echo base_url($module.'/berita');?>">
               <i class="icon md-rss" aria-hidden="true"></i>
               <span class="site-menu-title">Berita</span>
             </a>
          </li>

           <li class="site-menu-item <?php if(isset($pengumuman)){echo $pengumuman;} ?>">
             <a class="animsition-link" href="<?php echo base_url($module.'/pengumuman');?>">
               <i class="icon md-notifications" aria-hidden="true"></i>
               <span class="site-menu-title">Pengumuman</span>
             </a>
          </li>

           <li class="site-menu-item <?php if(isset($sk)){echo $sk;} ?>">
             <a class="animsition-link" href="<?php echo base_url($module.'/sk');?>">
               <i class="icon md-file-text" aria-hidden="true"></i>
               <span class="site-menu-title">Surat Keputusan</span>
             </a>
          </li>

            <?php /* Struktur Organisasi & Visi/Misi beranda.
                     Tautan lama (admin/organisasi) belum tersedia dan
                     admin/visimisi belum memiliki view, jadi diarahkan ke
                     halaman pengaturan beranda yang menanganinya. */ ?>
            <li class="site-menu-item <?php if(isset($organisasi) || (isset($ph_tab) && $ph_tab === 'organisasi')){echo 'active';} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/pengaturanhome/organisasi');?>">
                <i class="icon md-device-hub" aria-hidden="true"></i>
                <span class="site-menu-title">Struktur Organisasi</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($visimisi) || (isset($ph_tab) && $ph_tab === 'visimisi')){echo 'active';} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/pengaturanhome/visimisi');?>">
                <i class="icon md-eye" aria-hidden="true"></i>
                <span class="site-menu-title">Visi &amp; Misi</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($proker)){echo $proker;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/proker');?>">
                <i class="icon md-calendar-check" aria-hidden="true"></i>
                <span class="site-menu-title">Program Kerja</span>
              </a>
            </li>
         </ul>
        </li>

        <?php
        /* ==================== TAMPILAN BERANDA ====================
           Menu induk tersendiri. Tiap bagian adalah sub menu dengan halaman
           (URL) sendiri: admin/Pengaturanhome.php -> method render(). */
        $ph_menu = array(
            'identitas' => array('Identitas &amp; Tema',              'md-palette'),
            'hero'      => array('Hero / Banner',                     'md-image'),
            'akses'     => array('Kartu Akses',                       'md-card'),
            'profil'    => array('Profil &amp; Galeri',               'md-collection-image-o'),
            'tupoksi'   => array('Tupoksi',                           'md-assignment-account'),
            'sasaran'   => array('Sasaran Mutu',                      'md-flag'),
            'informasi' => array('Judul SK, Berita &amp; Pengumuman', 'md-view-headline'),
            'kontak'    => array('Kontak &amp; Footer',               'md-phone'),
            'section'   => array('Tampilkan Section',                 'md-view-module'),
        );
        ?>

        <li class="site-menu-item has-sub <?php if(isset($ph_tab) && !isset($konten_aktif)){echo 'active';} ?>">
          <a href="javascript:void(0)">
                  <i class="site-menu-icon md-desktop-mac" aria-hidden="true"></i>
                  <span class="site-menu-title">Tampilan Beranda</span>
                          <span class="site-menu-arrow"></span>
         </a>

         <ul class="site-menu-sub">
           <?php foreach ($ph_menu as $ph_slug => $ph_bagian): ?>
             <li class="site-menu-item <?php if(isset($ph_tab) && $ph_tab === $ph_slug){echo 'active';} ?>">
               <a class="animsition-link" href="<?php echo base_url($module.'/pengaturanhome/'.$ph_slug);?>">
                 <i class="icon <?php echo $ph_bagian[1]; ?>" aria-hidden="true"></i>
                 <span class="site-menu-title"><?php echo $ph_bagian[0]; ?></span>
               </a>
             </li>
           <?php endforeach; ?>
         </ul>
        </li>

        <li class="site-menu-item <?php if(isset($akun)){echo $akun;} ?>">
          <a class="animsition-link" href="<?php echo base_url($module.'/akun');?>">
                  <i class="site-menu-icon md-account-circle" aria-hidden="true"></i>
                  <span class="site-menu-title">Akun</span>
              </a>
        </li>
        
      </ul>
</div>

