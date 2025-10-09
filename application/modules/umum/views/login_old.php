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

        <link
            rel="apple-touch-icon"
            href="<?php echo asset_url();?>assets/images/apple-touch-icon.png">
        <link
            rel="shortcut icon"
            href="<?php echo asset_url();?>assets/images/favicon.ico">

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

                    <div class="brand">
                      <h2 class="brand-text font-size-18 text-center"><?php  echo "Formulir Permohonan Izin Penelitian";?></h2>
                    </div>
                    <br/>
                    <div class="form-group form-material" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_nama"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Nama</label>
                    </div>
                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_npm"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">NPM</label>
                    </div>
                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="email"
                            class="form-control"
                            name="ip_email"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Email</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_asalpt"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Asal Perguruan Tinggi</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_fakultas"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Fakultas</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_prodi"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Program Studi</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                    </div>
                    
                    <div class="form-group form-material floating">
                    
                        <input type="text" class="form-control" id="ip_tglsurat" name="ip_tglsurat">
                        <label class="floating-label">Tanggal Surat Permohonan</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_nosurat"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">No Surat Permohonan</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_judul"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Judul Penelitian</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_lokasi"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Lokasi Penelitian</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <label class="floating-label">File Surat Permohonan</label>
                        <input type="file" id="upload_file" name="upload_file" data-plugin="dropify"/>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">

                        <input
                            type="text"
                            class="form-control"
                            name="captcha"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Captcha</label>

                    </div>
                    <?php if(isset($captcha))echo $captcha; ?>

                    <button
                        id="validateButton"
                        type="submit"
                        class="btn btn-primary btn-block btn-lg mt-40"
                        name="submitbutton"
                        value="submit">Kirim</button>

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