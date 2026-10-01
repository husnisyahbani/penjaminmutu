<style type="text/css">
  /* Sub menu Pengaturan Home: tema hanya mengatur item level pertama,
     jadi item anak diberi indentasi + penanda sendiri. */
  .site-menu .site-menu-child > a { padding-left: 44px !important; font-size: 13px; }
  .site-menu .site-menu-child > a .site-menu-title:before { content: "–"; margin-right: 8px; opacity: .55; }
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
            
            <li class="site-menu-item <?php if(isset($organisasi)){echo $organisasi;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/organisasi');?>">
                <span class="site-menu-title">Struktur Organisasi</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($visimisi)){echo $visimisi;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/visimisi');?>">
                <span class="site-menu-title">Visi & Misi</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($proker)){echo $proker;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/proker');?>">
                <span class="site-menu-title">Program Kerja</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($pengaturanhome)){echo $pengaturanhome;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/pengaturanhome');?>">
                <span class="site-menu-title">Pengaturan Home</span>
              </a>
            </li>

            <?php
            /* Sub menu Pengaturan Home - setiap bagian punya halaman sendiri
               (lihat admin/Pengaturanhome.php pada method render()). */
            $ph_menu = array(
                'identitas'  => 'Identitas &amp; Tema',
                'hero'       => 'Hero / Banner',
                'akses'      => 'Kartu Akses',
                'profil'     => 'Profil &amp; Galeri',
                'visimisi'   => 'Visi &amp; Misi',
                'tupoksi'    => 'Tupoksi',
                'sasaran'    => 'Sasaran Mutu',
                'organisasi' => 'Pengelola &amp; Struktur',
                'informasi'  => 'SK, Berita &amp; Pengumuman',
                'kontak'     => 'Kontak &amp; Footer',
                'section'    => 'Tampilkan Section',
            );
            ?>

            <?php foreach ($ph_menu as $ph_slug => $ph_label): ?>
              <li class="site-menu-item site-menu-child <?php if(isset($ph_tab) && $ph_tab === $ph_slug){echo 'active';} ?>">
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

