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

            <li class="site-menu-item <?php if(isset($auditee)){echo $auditee;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/auditee');?>">
                <span class="site-menu-title">Pengendalian</span>
              </a>
            </li>

            <li class="site-menu-item <?php if(isset($auditee)){echo $auditee;} ?>">
              <a class="animsition-link" href="<?php echo base_url($module.'/auditee');?>">
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
        
        
        
        <li class="site-menu-item <?php if(isset($akun)){echo $akun;} ?>">
          <a class="animsition-link" href="<?php echo base_url($module.'/akun');?>">
                  <i class="site-menu-icon md-view-compact" aria-hidden="true"></i>
                  <span class="site-menu-title">Akun</span>
              </a>
        </li>
        
      </ul>
</div>

