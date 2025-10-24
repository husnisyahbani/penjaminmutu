<div class="site-menubar">
      <ul class="site-menu">
        
        <li class="site-menu-item <?php if(isset($dashboard)){echo $dashboard;} ?>">
          <a class="animsition-link" href="<?php echo base_url($module);?>">
                  <i class="site-menu-icon md-view-compact" aria-hidden="true"></i>
                  <span class="site-menu-title">Dashboard</span>
              </a>
        </li>

        <li class="site-menu-item <?php if(isset($ptk)){echo $ptk;} ?>">
          <a class="animsition-link" href="<?php echo base_url($module."/ptk");?>">
                  <i class="site-menu-icon md-view-compact" aria-hidden="true"></i>
                  <span class="site-menu-title">PTK</span>
              </a>
        </li>
        
        

        
        
        
        
        
      </ul>
</div>

