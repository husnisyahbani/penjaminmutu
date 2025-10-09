<!-- Core  -->
<script src="<?php echo asset_url();?>global/vendor/babel-external-helpers/babel-external-helpers.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/jquery/jquery.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/popper-js/umd/popper.min.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/bootstrap/bootstrap.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/animsition/animsition.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/mousewheel/jquery.mousewheel.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/asscrollbar/jquery-asScrollbar.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/asscrollable/jquery-asScrollable.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/ashoverscroll/jquery-asHoverScroll.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/waves/waves.js"></script>
    
    <!-- Plugins -->
    <script src="<?php echo asset_url();?>global/vendor/switchery/switchery.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/intro-js/intro.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/screenfull/screenfull.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/slidepanel/jquery-slidePanel.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/jquery-placeholder/jquery.placeholder.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/dropify/dropify.min.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/formvalidation/formValidation.min.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/formvalidation/framework/bootstrap4.min.js"></script>
    <script src="<?php echo asset_url(); ?>global/vendor/toastr/toastr.js"></script>
    <script src="<?php echo asset_url(); ?>global/vendor/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <!-- Scripts -->
    <script src="<?php echo asset_url();?>global/js/Component.js"></script>
    <script src="<?php echo asset_url();?>global/js/Plugin.js"></script>
    <script src="<?php echo asset_url();?>global/js/Base.js"></script>
    <script src="<?php echo asset_url();?>global/js/Config.js"></script>
    
    <script src="<?php echo asset_url();?>assets/js/Section/Menubar.js"></script>
    <script src="<?php echo asset_url();?>assets/js/Section/GridMenu.js"></script>
    <script src="<?php echo asset_url();?>assets/js/Section/Sidebar.js"></script>
    <script src="<?php echo asset_url();?>assets/js/Section/PageAside.js"></script>
    <script src="<?php echo asset_url();?>assets/js/Plugin/menu.js"></script>
    
    <script src="<?php echo asset_url();?>global/js/config/colors.js"></script>
    <script src="<?php echo asset_url();?>assets/js/config/tour.js"></script>
    <script>Config.set('assets', '<?php echo asset_url();?>assets');</script>
    
    <!-- Page -->
    <script src="<?php echo asset_url();?>assets/js/Site.js"></script>
    <script src="<?php echo asset_url();?>global/js/Plugin/asscrollable.js"></script>
    <script src="<?php echo asset_url();?>global/js/Plugin/slidepanel.js"></script>
    <script src="<?php echo asset_url();?>global/js/Plugin/switchery.js"></script>
        <script src="<?php echo asset_url();?>global/js/Plugin/jquery-placeholder.js"></script>
        <script src="<?php echo asset_url();?>global/js/Plugin/material.js"></script>
    
    <script>
      $(function () {
         
         <?php
if (isset($pesanberhasil)) {
    echo "toastr.options.timeOut = 'false';
        toastr.options.closeButton = true;
        toastr.options.positionClass = 'toast-top-right';
toastr['success']('" . $pesanberhasil . "');";
}
?>
<?php
if (isset($pesanerror)) {
    echo "toastr.options.timeOut = 'false';
        toastr.options.closeButton = true;
        toastr.options.positionClass = 'toast-top-right';
toastr['error']('" . $pesanerror . "');";
}
?>
     });
     
      (function(document, window, $){
        'use strict';
    
        var Site = window.Site;
        $(document).ready(function(){
          Site.run();
        });
      })(document, window, jQuery);
    </script>

<script type="text/javascript">
    var base_url = '<?php echo base_url('umum'); ?>';
    var root_url = '<?php echo base_url(); ?>';
 </script>

<?php
/**
 * Load js from loader core
 *
 * @return CI_OUTPUT
 * */
if (isset($js) == !FALSE) : foreach ($js as $file) :
        ?>
        <script src="<?php echo $file; ?>"></script>
        <?php
    endforeach;
endif;
?>
    
  </body>
</html>