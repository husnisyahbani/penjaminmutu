<div class="site-menubar">
      <ul class="site-menu">
        
        <li class="site-menu-item <?php if(isset($dashboard)){echo $dashboard;} ?>">
          <a class="animsition-link" href="<?php echo base_url($module);?>">
                  <i class="site-menu-icon md-view-compact" aria-hidden="true"></i>
                  <span class="site-menu-title">Dashboard</span>
              </a>
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
              <a class="animsition-link" href="<?php echo base_url($module.'/audit');?>">
                <span class="site-menu-title">Daftar Audit</span>
              </a>
            </li>
            
         </ul>
        </li>
        
        
        
        
      </ul>
</div>

