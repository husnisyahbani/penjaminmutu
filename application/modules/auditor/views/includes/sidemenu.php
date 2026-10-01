<div class="site-menubar">
      <ul class="site-menu">
        <li class="site-menu-item <?php if(isset($audit)){echo $audit;} ?>">
          <a class="animsition-link" href="<?php echo base_url($module)."/daftaraudit";?>">
                  <i class="site-menu-icon md-assignment-check" aria-hidden="true"></i>
                  <span class="site-menu-title">Daftar Audit</span>
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


        
        
        
        
      </ul>
</div>

