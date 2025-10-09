<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="description" content="bootstrap material admin template">
        <meta name="author" content="">

        <title>Dinas Pendidikan Provinsi Sumatera Selatan</title>

        <link rel="apple-touch-icon" href="<?php echo asset_url();?>/assets/images/logorsud.png">
    <link rel="shortcut icon" href="<?php echo asset_url();?>/assets/images/logorsud.png">

        <!-- Stylesheets -->
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>global/css/bootstrap.min.css">
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>global/css/bootstrap-extend.min.css">
        <link rel="stylesheet" href="<?php echo asset_url();?>assets/css/site.min.css">

        <!-- Plugins -->
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>global/vendor/animsition/animsition.css">
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>global/vendor/asscrollable/asScrollable.css">
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>global/vendor/switchery/switchery.css">
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>global/vendor/intro-js/introjs.css">
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>global/vendor/slidepanel/slidePanel.css">
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>global/vendor/jquery-mmenu/jquery-mmenu.css">
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>global/vendor/flag-icon-css/flag-icon.css">
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>global/vendor/waves/waves.css">
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>assets/examples/css/pages/email.css">

        <!-- Fonts -->
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>global/fonts/material-design/material-design.min.css">
        <link
            rel="stylesheet"
            href="<?php echo asset_url();?>global/fonts/brand-icons/brand-icons.min.css">
        <link
            rel='stylesheet'
            href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>

        <!--[if lt IE 9]> <script src="<?php echo
        asset_url();?>global/vendor/html5shiv/html5shiv.min.js"></script> <![endif]-->

        <!--[if lt IE 10]> <script src="<?php echo
        asset_url();?>global/vendor/media-match/media.match.min.js"></script> <script
        src="<?php echo asset_url();?>global/vendor/respond/respond.min.js"></script>
        <![endif]-->

        <!-- Scripts -->
        <script src="<?php echo asset_url();?>global/vendor/breakpoints/breakpoints.js"></script>
        <script>
            Breakpoints();
        </script>
    </head>
    <body
        class="animsition page-email page-email-welcome layout-full"
        style='background-image: url("<?php echo asset_url();?>/assets/examples/images/home.jpg");background-repeat: no-repeat;
  background-attachment: fixed;
  background-size: 100% 100%;'>
        <!--[if lt IE 8]> <p class="browserupgrade">You are using an
        <strong>outdated</strong> browser. Please <a
        href="http://browsehappy.com/">upgrade your browser</a> to improve your
        experience.</p> <![endif]-->

        <!-- Page -->
        <div class="page">
            <div class="page-content">
                <!-- Panel -->
                <div class="panel">
                    <div class="panel-body container-fluid">
                        <div class="email-title">
                            <img
                                class="brand-img"
                                src="<?php echo asset_url();?>/assets/images/logorsud.png"
                                alt="DINAS PENDIDIKAN SUMSEL"
                                width="120px">
                        </div>
                        <div class="card welcome-content">
                            <div class="card-block pl-40 pr-40  mt-30 mb-30">
                                <h2>Dinas Pendidikan Sumatera Selatan Seksi Kurikulum</h2>
                                <!-- <p>
                                    Selamat datang di situs resmi dinas pendidikan sumatera selatan bidang
                                    kurikulum, situs ini memberikan layanan terkait bidang kurikulum
                                </p> -->
                                <a class="btn btn-round btn-danger card-link" href="<?php echo base_url('login');?>">Sign In</a>
                            </div>
                            <div class="card-block">
                                <h2>Layanan Kami!</h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card card-shadow border">
                                            <div class="card-header cover">
                                                <img
                                                    class="cover-image"
                                                    src="<?php echo asset_url();?>assets/examples/images/konsultasi.png"
                                                    alt="...">
                                            </div>
                                            
                                            <div class="card-block">
                                                <a href="<?php echo base_url('konsultasi');?>" class="btn btn-round btn-success card-link">Konsultasi</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card card-shadow border">
                                            <div class="card-header cover">
                                                <img
                                                    class="cover-image"
                                                    src="<?php echo asset_url();?>assets/examples/images/surat.png"
                                                    alt="...">
                                            </div>
                                            
                                            <div class="card-block">
                                                <a href="<?php echo base_url('izinpenelitian');?>" class="btn btn-round btn-success">Ijin Penelitian</a>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <!-- End Panel -->
            </div>
        </div>
        <!-- End Page -->

        <script
            src="<?php echo asset_url();?>global/vendor/babel-external-helpers/babel-external-helpers.js"></script>
        <script src="<?php echo asset_url();?>global/vendor/jquery/jquery.js"></script>
        <script
            src="<?php echo asset_url();?>global/vendor/popper-js/umd/popper.min.js"></script>
        <script src="<?php echo asset_url();?>global/vendor/bootstrap/bootstrap.js"></script>
        <script src="<?php echo asset_url();?>global/vendor/animsition/animsition.js"></script>
        <script
            src="<?php echo asset_url();?>global/vendor/mousewheel/jquery.mousewheel.js"></script>
        <script
            src="<?php echo asset_url();?>global/vendor/asscrollbar/jquery-asScrollbar.js"></script>
        <script
            src="<?php echo asset_url();?>global/vendor/asscrollable/jquery-asScrollable.js"></script>
        <script src="<?php echo asset_url();?>global/vendor/waves/waves.js"></script>

        <!-- Plugins -->
        <script
            src="<?php echo asset_url();?>global/vendor/jquery-mmenu/jquery.mmenu.min.all.js"></script>
        <script src="<?php echo asset_url();?>global/vendor/switchery/switchery.js"></script>
        <script src="<?php echo asset_url();?>global/vendor/intro-js/intro.js"></script>
        <script src="<?php echo asset_url();?>global/vendor/screenfull/screenfull.js"></script>
        <script
            src="<?php echo asset_url();?>global/vendor/slidepanel/jquery-slidePanel.js"></script>

        <!-- Scripts -->
        <script src="<?php echo asset_url();?>global/js/Component.js"></script>
        <script src="<?php echo asset_url();?>global/js/Plugin.js"></script>
        <script src="<?php echo asset_url();?>global/js/Base.js"></script>
        <script src="<?php echo asset_url();?>global/js/Config.js"></script>

        <script src="<?php echo asset_url();?>assets/js/Section/Menubar.js"></script>
        <script src="<?php echo asset_url();?>assets/js/Section/Sidebar.js"></script>
        <script src="<?php echo asset_url();?>assets/js/Section/PageAside.js"></script>
        <script src="<?php echo asset_url();?>assets/js/Section/GridMenu.js"></script>

        <!-- Config -->
        <script src="<?php echo asset_url();?>global/js/config/colors.js"></script>
        <script src="<?php echo asset_url();?>assets/js/config/tour.js"></script>
        <script>
            Config.set('assets', '<?php echo asset_url();?>assets');
        </script>

        <!-- Page -->
        <script src="<?php echo asset_url();?>assets/js/Site.js"></script>
        <script src="<?php echo asset_url();?>global/js/Plugin/asscrollable.js"></script>
        <script src="<?php echo asset_url();?>global/js/Plugin/slidepanel.js"></script>
        <script src="<?php echo asset_url();?>global/js/Plugin/switchery.js"></script>

        <script>
            (function (document, window, $) {
                'use strict';

                var Site = window.Site;
                $(document).ready(function () {
                    Site.run();
                });
            })(document, window, jQuery);
        </script>

    </body>
</html>