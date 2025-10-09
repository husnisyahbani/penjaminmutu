<?php
include 'header.php';
?>

        <!-- Page -->
        <div class="page">
            <div class="page-content container-fluid">
                <div class="page-content container-fluid">
                    <div class="row">


                        <div class="col-lg-12">
                            <!-- Panel Wizard Form Container -->
                            <div class="panel">
                                <div class="panel-heading">
                                    <h3 class="panel-title">UPLOAD DATA WISUDAWAN</h3>
                                </div>
                                <div class="panel-body">
                                    <form class="form-horizontal" id="exampleStandardForm" action="<?php echo base_url() ?>admin/dashboard/simpanexcel" method="post" enctype="multipart/form-data">
           
                                        
                                        
                                        <div class="form-group row">
                                            <label class="col-md-3 form-control-label">Tanggal Lulus</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" name="tanggal_lulus" data-plugin="datepicker" data-date-format="dd-mm-yyyy" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-md-3 form-control-label">Bukti Bayar (max. 1MB)</label>
                                            <div class="col-md-9">
                                                <input type="file" class="form-control" name="upload_file" />
                                            </div>
                                        </div>
                                        

                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary" id="validateButton2">Kirim</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- End Panel Wizard Form Container -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page -->

        

        <!-- Footer -->
        <footer class="site-footer">
            <div class="site-footer-legal">© 2018 <a href="http://husnisyahbani.blogspot.com">M HUSNI SYAHBANI</a></div>
            <div class="site-footer-right">
                Crafted with <i class="red-600 wb wb-heart"></i> by <a href="https://stik-sitikhadijah.ac.id">STIK SITI KHADIJAH</a>
            </div>
        </footer>
        <!-- Core  -->
        <script src="<?php echo asset_url() . 'global/vendor/babel-external-helpers/babel-external-helpers.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/jquery/jquery.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/popper-js/umd/popper.min.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/bootstrap/bootstrap.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/animsition/animsition.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/mousewheel/jquery.mousewheel.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/asscrollbar/jquery-asScrollbar.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/asscrollable/jquery-asScrollable.js'; ?>"></script>

        <!-- Plugins -->
        <script src="<?php echo asset_url() . 'global/vendor/switchery/switchery.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/intro-js/intro.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/screenfull/screenfull.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/slidepanel/jquery-slidePanel.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/chartist/chartist.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/aspieprogress/jquery-asPieProgress.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/matchheight/jquery.matchHeight-min.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/jquery-selective/jquery-selective.min.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/bootstrap-datepicker/bootstrap-datepicker.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/jquery-wizard/jquery-wizard.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/formvalidation/formValidation.min.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/formvalidation/framework/bootstrap4.min.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/vendor/toastr/toastr.js'; ?>"></script>

        <!-- Scripts -->
        <script src="<?php echo asset_url() . 'global/js/Component.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/js/Plugin.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/js/Base.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/js/Config.js'; ?>"></script>

        <script src="<?php echo asset_url() . 'assets/js/Section/Menubar.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'assets/js/Section/Sidebar.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'assets/js/Section/PageAside.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'assets/js/Plugin/menu.js'; ?>"></script>

        <!-- Config -->
        <script src="<?php echo asset_url() . 'global/js/config/colors.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'assets/js/config/tour.js'; ?>"></script>
        <script>Config.set('assets', '../assets');</script>

        <!-- Page -->
        <script src="<?php echo asset_url() . 'assets/js/Site.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/js/Plugin/asscrollable.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/js/Plugin/slidepanel.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/js/Plugin/switchery.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/js/Plugin/matchheight.js'; ?>"></script>       
        <script src="<?php echo asset_url() . 'assets/examples/js/forms/wizard.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/js/Plugin/aspieprogress.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/js/Plugin/bootstrap-datepicker.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/js/Plugin/asscrollable.js'; ?>"></script>

        <script src="<?php echo asset_url() . 'assets/examples/js/dashboard/team.js'; ?>"></script>
        <script src="<?php echo asset_url() . 'global/js/Plugin/toastr.js'; ?>"></script>

        <script type="text/javascript">

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

                $('#exampleStandardForm').formValidation({
                    framework: "bootstrap4",
                    button: {
                        selector: '#validateButton2',
                        disabled: 'disabled'
                    },
                    icon: null,
                    fields: {
                        tanggal_lulus: {
                            validators: {
                                notEmpty: {
                                    message: 'Wajib Diisi'
                                }
                            }
                        },
                        
                        upload_file: {
                            validators: {
                                notEmpty: {
                                    message: 'Wajib Diisi'
                                }
                            }
                        }
                    },
                    err: {
                        clazz: 'invalid-feedback'
                    },
                    control: {
                        // The CSS class for valid control
                        valid: 'is-valid',
                        // The CSS class for invalid control
                        invalid: 'is-invalid'
                    },
                    row: {
                        invalid: 'has-danger'
                    }
                });

            });

        </script>

    </body>
</html>
