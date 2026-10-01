<style type="text/css">
  /* Label kelompok di dalam sub menu Website (mis. "Tampilan Beranda"). */
  .site-menu .site-menu-group {
    padding: 14px 25px 6px;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .13em;
    text-transform: uppercase;
    color: rgba(15, 23, 42, .38);
    pointer-events: none;
  }
  .site-menubar-dark .site-menu .site-menu-group { color: rgba(255, 255, 255, .45); }
</style>

<div class="site-menubar">
      <ul class="site-menu">
        
        <li class="site-menu-item has-sub <?php if(isset($data)){echo $data;} ?>">
          <a href="javascript:void(0)">
                  <i class="site-menu-icon md-view-compact" aria-hidden="true"></i>
                  <span class="site-menu-title">Dokument Mutu</span>
                          <span class="site-menu-arrow"></span>
         </a>

         <ul class="site-menu-sub">
           <li class="site-menu-item <?php if(isset($semua)){echo $semua;} ?>">
             <a class="animsition-link" href="<?php echo base_url($module.'/data');?>">
               <span class="site-menu-title">Semua</span>
             </a>
          </li>
          
        
           <li class="site-menu-item <?php if(isset($penetapan)){echo $penetapan;} ?>">
             <a class="animsition-link" href="<?php echo base_url($module.'/penetapan');?>">
               <span class="site-menu-title">Penetapan</span>
             </a>
          </li>
            
            <li class="site-menu-item <?php if(isset($pelaksanaan)){echo $pelaksanaan;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/pelaksanaan');?>">
                <span class="site-menu-title">Pelaksanaan</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($evaluasi)){echo $evaluasi;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/evaluasi');?>">
                <span class="site-menu-title">Evaluasi</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($pengendalian)){echo $pengendalian;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/pengendalian');?>">
                <span class="site-menu-title">Pengendalian</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($peningkatan)){echo $peningkatan;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/peningkatan');?>">
                <span class="site-menu-title">Peningkatan</span>
              </a>
            </li>
            
         </ul>
        </li>

        
        
        

        <li class="site-menu-item has-sub <?php if(isset($auditmenu)){echo $auditmenu;} ?>">
          <a href="javascript:void(0)">
                  <i class="site-menu-icon md-view-compact" aria-hidden="true"></i>
                  <span class="site-menu-title">Audit</span>
                          <span class="site-menu-arrow"></span>
         </a>
          
         <ul class="site-menu-sub">
           <li class="site-menu-item <?php if(isset($formaudit)){echo $formaudit;} ?>">
             <a class="animsition-link" href="<?php echo base_url($module.'/formaudit');?>">
               <span class="site-menu-title">Formulir Audit</span>
             </a>
        </li>
            
            <li class="site-menu-item <?php if(isset($audit)){echo $audit;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/daftaraudit');?>">
                <span class="site-menu-title">Daftar Audit</span>
              </a>
            </li>
            
         </ul>
        </li>


        <li class="site-menu-item has-sub <?php if(isset($website)){echo $website;} ?>">
          <a href="javascript:void(0)">
                  <i class="site-menu-icon md-view-compact" aria-hidden="true"></i>
                  <span class="site-menu-title">Website</span>
                          <span class="site-menu-arrow"></span>
         </a>

         <ul class="site-menu-sub">
           <li class="site-menu-item <?php if(isset($berita)){echo $berita;} ?>">
             <a class="animsition-link" href="<?php echo base_url($module.'/berita');?>">
               <span class="site-menu-title">Berita</span>
             </a>
          </li>

          
           <li class="site-menu-item <?php if(isset($pengumuman)){echo $pengumuman;} ?>">
             <a class="animsition-link" href="<?php echo base_url($module.'/pengumuman');?>">
               <span class="site-menu-title">Pengumuman</span>
             </a>
          </li>
          
        
           <li class="site-menu-item <?php if(isset($sk)){echo $sk;} ?>">
             <a class="animsition-link" href="<?php echo base_url($module.'/sk');?>">
               <span class="site-menu-title">Surat Keputusan</span>
             </a>
          </li>
            
            <?php /* Struktur Organisasi & Visi/Misi beranda.
                     Tautan lama (admin/organisasi) belum tersedia dan
                     admin/visimisi belum memiliki view, jadi diarahkan ke
                     halaman pengaturan beranda yang menanganinya. */ ?>
            <li class="site-menu-item <?php if(isset($organisasi) || (isset($ph_tab) && $ph_tab === 'organisasi')){echo 'active';} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/pengaturanhome/organisasi');?>">
                <span class="site-menu-title">Struktur Organisasi</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($visimisi) || (isset($ph_tab) && $ph_tab === 'visimisi')){echo 'active';} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/pengaturanhome/visimisi');?>">
                <span class="site-menu-title">Visi &amp; Misi</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($proker)){echo $proker;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/proker');?>">
                <span class="site-menu-title">Program Kerja</span>
              </a>
            </li>

            <?php
            /* Pengaturan tampilan BERANDA - tiap bagian adalah menu tersendiri
               (tidak ada menu induk "Pengaturan Home").
               Halaman: admin/Pengaturanhome.php -> method render(). */
            $ph_menu = array(
                'identitas' => 'Identitas &amp; Tema',
                'hero'      => 'Hero / Banner',
                'akses'     => 'Kartu Akses',
                'profil'    => 'Profil &amp; Galeri',
                'tupoksi'   => 'Tupoksi',
                'sasaran'   => 'Sasaran Mutu',
                'informasi' => 'Judul SK, Berita &amp; Pengumuman',
                'kontak'    => 'Kontak &amp; Footer',
                'section'   => 'Tampilkan Section',
            );
            ?>

            <li class="site-menu-group">Tampilan Beranda</li>

            <?php foreach ($ph_menu as $ph_slug => $ph_label): ?>
              <li class="site-menu-item <?php if(isset($ph_tab) && $ph_tab === $ph_slug){echo 'active';} ?>">
                <a class="animsition-link" href="<?php echo base_url($module.'/pengaturanhome/'.$ph_slug);?>">
                  <span class="site-menu-title"><?php echo $ph_label; ?></span>
                </a>
              </li>
            <?php endforeach; ?>

            
            
         </ul>
        </li>
        
        
        
        <li class="site-menu-item <?php if(isset($akun)){echo $akun;} ?>">
          <a class="animsition-link" href="<?php echo base_url($module.'/akun');?>">
                  <i class="site-menu-icon md-view-compact" aria-hidden="true"></i>
                  <span class="site-menu-title">Akun</span>
              </a>
        </li>
        
      </ul>
</div>

