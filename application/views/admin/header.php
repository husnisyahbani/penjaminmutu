<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="description" content="bootstrap admin template">
        <meta name="author" content="">

        <title>PMB STIK SITI KHADIJAH</title>

        <link rel="apple-touch-icon" href="https://stik-sitikhadijah.ac.id/wp-content/uploads/2019/05/Logo-Sekolah-Tinggi-Ilmu-Kesehatan-Siti-Khadijah.png">
        <link rel="shortcut icon" href="https://stik-sitikhadijah.ac.id/wp-content/uploads/2019/05/Logo-Sekolah-Tinggi-Ilmu-Kesehatan-Siti-Khadijah.png">

        <!-- Stylesheets -->
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/css/bootstrap.min.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/css/bootstrap-extend.min.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'assets/css/site.min.css'; ?>">

        <!-- Plugins -->
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/animsition/animsition.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/asscrollable/asScrollable.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/switchery/switchery.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/intro-js/introjs.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/slidepanel/slidePanel.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/flag-icon-css/flag-icon.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/chartist/chartist.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/aspieprogress/asPieProgress.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/jquery-selective/jquery-selective.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/bootstrap-datepicker/bootstrap-datepicker.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/asscrollable/asScrollable.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'assets/examples/css/dashboard/team.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/formvalidation/formValidation.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'assets/examples/css/forms/validation.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/toastr/toastr.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'assets/examples/css/advanced/toastr.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/sweetalert/sweetalert2.min.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/datatables.net-bs4/dataTables.bootstrap4.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/datatables.net-fixedheader-bs4/dataTables.fixedheader.bootstrap4.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/datatables.net-fixedcolumns-bs4/dataTables.fixedcolumns.bootstrap4.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/datatables.net-rowgroup-bs4/dataTables.rowgroup.bootstrap4.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/datatables.net-scroller-bs4/dataTables.scroller.bootstrap4.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/datatables.net-select-bs4/dataTables.select.bootstrap4.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/datatables.net-responsive-bs4/dataTables.responsive.bootstrap4.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/datatables.net-buttons-bs4/dataTables.buttons.bootstrap4.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'assets/examples/css/tables/datatable.css'; ?>">
    
    
    <!-- Fonts -->
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/fonts/font-awesome/font-awesome.css'; ?>">


        <!-- Fonts -->
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/fonts/web-icons/web-icons.min.css'; ?>">
        <link rel="stylesheet" href="<?php echo asset_url() . 'global/fonts/brand-icons/brand-icons.min.css'; ?>">
        <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>

        <!--[if lt IE 9]>
        <script src="<?php echo asset_url() . 'global/vendor/html5shiv/html5shiv.min.js'; ?>"></script>
        <![endif]-->

        <!--[if lt IE 10]>
        <script src="<?php echo asset_url() . 'global/vendor/media-match/media.match.min.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/respond/respond.min.js'; ?>"></script>
        <![endif]-->

        <!-- Scripts -->

        <script src="<?php echo asset_url() . 'global/vendor/breakpoints/breakpoints.js'; ?>"></script>
        <script>
            Breakpoints();
        </script>
    </head>
    <body class="animsition site-navbar-small dashboard">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega navbar-inverse"
      role="navigation">
    
      <div class="navbar-header">
        <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided"
          data-toggle="menubar">
          <span class="sr-only">Toggle navigation</span>
          <span class="hamburger-bar"></span>
        </button>
        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
          data-toggle="collapse">
          <i class="icon wb-more-horizontal" aria-hidden="true"></i>
        </button>
        <a class="navbar-brand navbar-brand-center" href="<?php echo base_url().'admin/dashboard';?>">
          <img class="navbar-brand-logo navbar-brand-logo-normal" src="https://stik-sitikhadijah.ac.id/wp-content/uploads/2019/05/Logo-Sekolah-Tinggi-Ilmu-Kesehatan-Siti-Khadijah.png"
            title="Remark">
          <img class="navbar-brand-logo navbar-brand-logo-special" src="https://stik-sitikhadijah.ac.id/wp-content/uploads/2019/05/Logo-Sekolah-Tinggi-Ilmu-Kesehatan-Siti-Khadijah.png"
            title="Remark">
          <span class="navbar-brand-text hidden-xs-down"> STIK SITI KHADIJAH</span>
        </a>
        
      </div>
    
      <div class="navbar-container container-fluid">
        <!-- Navbar Collapse -->
        <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
          <!-- Navbar Toolbar -->
          <ul class="nav navbar-toolbar">
            <li class="nav-item hidden-float" id="toggleMenubar">
              <a class="nav-link" data-toggle="menubar" href="#" role="button">
                <i class="icon hamburger hamburger-arrow-left">
                  <span class="sr-only">Toggle menubar</span>
                  <span class="hamburger-bar"></span>
                </i>
              </a>
            </li>
            
            
          </ul>
          <!-- End Navbar Toolbar -->
    
          <!-- Navbar Toolbar Right -->
          <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
            
            <li class="nav-item dropdown">
              <a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false"
                data-animation="scale-up" role="button">
                <span class="avatar avatar-online">
                  <img src="https://stik-sitikhadijah.ac.id/wp-content/uploads/2019/05/Logo-Sekolah-Tinggi-Ilmu-Kesehatan-Siti-Khadijah.png" alt="...">
                  <i></i>
                </span>
              </a>
              <div class="dropdown-menu" role="menu">
                
                  <a class="dropdown-item" href="<?php echo base_url()."admin/dashboard/logout";?>" role="menuitem"><i class="icon wb-power" aria-hidden="true"></i> Logout</a>
              </div>
            </li>
            
            
            
          </ul>
          <!-- End Navbar Toolbar Right -->
        </div>
        <!-- End Navbar Collapse -->
    
        <!-- Site Navbar Seach -->
        <div class="collapse navbar-search-overlap" id="site-navbar-search">
          <form role="search">
            <div class="form-group">
              <div class="input-search">
                <i class="input-search-icon wb-search" aria-hidden="true"></i>
                <input type="text" class="form-control" name="site-search" placeholder="Search...">
                <button type="button" class="input-search-close icon wb-close" data-target="#site-navbar-search"
                  data-toggle="collapse" aria-label="Close"></button>
              </div>
            </div>
          </form>
        </div>
        <!-- End Site Navbar Seach -->
      </div>
    </nav>    
        
        <div class="site-menubar site-menubar-light">
            <div class="site-menubar-body">
                <div>
                    <div>
                        <ul class="site-menu" data-plugin="menu">
                            


                            <li class="dropdown site-menu-item has-sub">
                                <a data-toggle="dropdown" href="javascript:void(0)" data-dropdown-toggle="false">
                                    <i class="site-menu-icon wb-extension" aria-hidden="true"></i>
                                    <span class="site-menu-title">Tambah Data</span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <div class="dropdown-menu">
                                    <div class="site-menu-scroll-wrap is-list">
                                        <div>
                                            <div>
                                                <ul class="site-menu-sub site-menu-normal-list">
                                                    <li class="site-menu-item">
                                                        <a class="animsition-link" href="<?php echo base_url()."admin/dashboard/data" ?>">
                                                            <span class="site-menu-title">Tambah Data Baru</span>
                                                        </a>
                                                    </li>
                                                    
                                                    <li class="site-menu-item">
                                                        <a class="animsition-link" href="<?php echo base_url()."admin/dashboard/excel" ?>">
                                                            <span class="site-menu-title">Upload Excel</span>
                                                        </a>
                                                    </li>

                                                    

                                                    

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>



                            

                        </ul>      
                    </div>
                </div>
            </div>
        </div>