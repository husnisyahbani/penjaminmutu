
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="STIK SITI KHADIJAH">
    <meta name="author" content="">
    
    <title><?php echo $title;?></title>
    
    <link rel="apple-touch-icon" href="<?php echo asset_url();?>/assets/images/logostik.png">
    <link rel="shortcut icon" href="<?php echo asset_url();?>/assets/images/logostik.png">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo asset_url();?>global/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/css/bootstrap-extend.min.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>assets/css/site.min.css">


<style>
/* CSS jangan taruh di dalam <script> */
.tox {
  z-index: 2000 !important;
}
</style>

    <!-- Plugins -->
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/animsition/animsition.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/asscrollable/asScrollable.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/switchery/switchery.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/intro-js/introjs.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/slidepanel/slidePanel.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/jquery-mmenu/jquery-mmenu.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/flag-icon-css/flag-icon.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/waves/waves.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/chartist/chartist.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>assets/examples/css/dashboard/v1.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/owl-carousel/owl.carousel.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/slick-carousel/slick.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>assets/examples/css/uikit/carousel.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/sweetalert/sweetalert2.min.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/blueimp-file-upload/jquery.fileupload.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/dropify/dropify.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/formvalidation/formValidation.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>assets/examples/css/forms/validation.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>summernote/summernote-bs4.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>assets/examples/css/uikit/modals.css">
    <link rel="stylesheet" href="<?php echo asset_url(); ?>global/vendor/toastr/toastr.min.css">
     <link rel="stylesheet" href="<?php echo asset_url(); ?>global/vendor/bootstrap-datepicker/bootstrap-datepicker.css">
    
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/datatables.net-fixedheader-bs4/dataTables.fixedheader.bootstrap4.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/datatables.net-fixedcolumns-bs4/dataTables.fixedcolumns.bootstrap4.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/datatables.net-rowgroup-bs4/dataTables.rowgroup.bootstrap4.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/datatables.net-scroller-bs4/dataTables.scroller.bootstrap4.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/datatables.net-select-bs4/dataTables.select.bootstrap4.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/datatables.net-responsive-bs4/dataTables.responsive.bootstrap4.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/vendor/datatables.net-buttons-bs4/dataTables.buttons.bootstrap4.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>assets/examples/css/tables/datatable.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    
    <!-- Fonts -->
    <link rel="stylesheet" href="<?php echo asset_url();?>global/fonts/material-design/material-design.min.css">
    <link rel="stylesheet" href="<?php echo asset_url();?>global/fonts/brand-icons/brand-icons.min.css">
    <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
    
    <!--[if lt IE 9]>
    <script src="<?php echo asset_url();?>global/vendor/html5shiv/html5shiv.min.js"></script>
    <![endif]-->
    
    <!--[if lt IE 10]>
    <script src="<?php echo asset_url();?>global/vendor/media-match/media.match.min.js"></script>
    <script src="<?php echo asset_url();?>global/vendor/respond/respond.min.js"></script>
    <![endif]-->
    
    <!-- Scripts -->
    <script src="<?php echo asset_url();?>global/vendor/breakpoints/breakpoints.js"></script>
    <script>
      Breakpoints();
    </script>
  </head>
  <body class="animsition site-navbar-small dashboard">