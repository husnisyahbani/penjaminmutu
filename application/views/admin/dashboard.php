<?php
include 'header.php';
?>

<div class="page">

    <div class="page-content container-fluid">
        <div class="row">
            <!-- First Row -->
            <div class="col-xl-4 col-md-8 info-panel">
                <div class="card card-shadow">
                    <div class="card-block bg-white p-20">
                        <button type="button" class="btn btn-floating btn-sm btn-primary">
                            <i class="icon wb-user"></i>
                        </button>
                        <span class="ml-15 font-weight-400">TOTAL TAGIHAN</span>
                        <div class="content-text text-center mb-0">
                            <span class="font-size-40 font-weight-100"><?php
                                if (isset($daftar)) {
                                    echo $daftar;
                                } else {
                                    echo "0";
                                }
                                ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-8 info-panel">
                <div class="card card-shadow">
                    <div class="card-block bg-white p-20">
                        <button type="button" class="btn btn-floating btn-sm btn-success">
                            <i class="icon wb-user"></i>
                        </button>
                        <span class="ml-15 font-weight-400">TAGIHAN LUNAS</span>
                        <div class="content-text text-center mb-0">
                            <span class="font-size-40 font-weight-100"><?php
                                if (isset($valid)) {
                                    echo $valid;
                                } else {
                                    echo "0";
                                }
                                ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-8 info-panel">
                <div class="card card-shadow">
                    <div class="card-block bg-white p-20">
                        <button type="button" class="btn btn-floating btn-sm btn-danger">
                            <i class="icon wb-user"></i>
                        </button>
                        <span class="ml-15 font-weight-400">TAGIHAN BELUM LUNAS</span>
                        <div class="content-text text-center mb-0">
                            <span class="font-size-40 font-weight-100"><?php
                                if (isset($gagal)) {
                                    echo $gagal;
                                } else {
                                    echo "0";
                                }
                                ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- End First Row -->



        </div>

        <div class="panel">
            <header class="panel-heading">
                <div class="panel-actions"></div>
                <h3 class="panel-title">Tagihan</h3>
            </header>
            <div class="panel-body">
                <table class="table table-hover dataTable table-striped w-full" id="pendaftar">
                    <thead>
                        <tr>
                            <th>No Pembayaran</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Total Tagihan</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        
        
    </div>
</div>



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

<script src="<?php echo asset_url() . 'global/vendor/datatables.net/jquery.dataTables.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-bs4/dataTables.bootstrap4.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-fixedheader/dataTables.fixedHeader.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-fixedcolumns/dataTables.fixedColumns.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-rowgroup/dataTables.rowGroup.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-scroller/dataTables.scroller.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-responsive/dataTables.responsive.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-responsive-bs4/responsive.bootstrap4.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-buttons/dataTables.buttons.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-buttons/buttons.html5.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-buttons/buttons.flash.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-buttons/buttons.print.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-buttons/buttons.colVis.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/datatables.net-buttons-bs4/buttons.bootstrap4.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/asrange/jquery-asRange.min.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/vendor/bootbox/bootbox.js'; ?>"></script>

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
<script>Config.set('assets', '../../assets');</script>

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
<script src="<?php echo asset_url() . 'global/js/Plugin/sweetalert2.min.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/js/Plugin/datatables.js'; ?>"></script>

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
$.fn.dataTable.ext.errMode = 'throw';

                                $('#pendaftar').DataTable({
                                    "responsive": true,
                                    "processing": true,
                                    "serverSide": true,
                                    "searching": true,
                                    "order": [],
                                    "columnDefs": [
                                        {}
                                    ],
                                    "ajax": {
                                        "url": "<?php echo base_url(); ?>admin/dashboard/listtagihan",
                                        "type": "POST"
                                    }
                                });
                                
                                
                                
                                

                            });
                            
                            
                            $("#pendaftarvalid").on("click", ".printijazah", function () {
                                var id = $(this).attr('id');
                                print(id);
                                //window.open('<?php echo base_url();?>'+"admin/dashboard/ijazah/"+id);
                            });

                            $("#pendaftarvalid").on("click", ".print", function () {
                                var id = $(this).attr('id');
                                //print(id);
                                window.open('<?php echo base_url();?>'+"admin/dashboard/beritaacara/"+id);
                            });

                            $("#pendaftar").on("click", ".valid", function () {
                                var id = $(this).attr('id');
                                valid(id);
                            });


                            $("#pendaftar").on("click", ".delete", function () {
                                var id = $(this).attr('id');
                                hapus(id);
                            });
                            
                            $("#pendaftar").on("click", ".edit", function () {
                                var id = $(this).attr('id');
                                window.open('<?php echo base_url()."admin/dashboard/data/";?>'+id);
                            });


                            function print($id)
                            {
                                swal.fire({
                                    title: "No Urut Ijazah",
                                    input: 'number',
                                    inputAttributes: {
                                        autocapitalize: 'off'
                                    },
                                    showCancelButton: true,
                                    confirmButtonText: "Print",
                                    cancelButtonText: 'Batal',
                                    preConfirm: function (no) {
                                        window.open('<?php echo base_url();?>'+"admin/dashboard/ijazah?id="+$id+"&no="+no);
                                        //location.href = '<?php echo base_url();?>'+"admin/dashboard/cetakkartu?id="+$id+"&no="+no;
                                    }
                                });
                            }


                            function valid($id)
                            {
                                swal.fire({
                                    title: "Anda Yakin?",
                                    text: "Anda Yakin Ingin Memvalidasi Pendaftar Ini?",
                                    type: "warning",
                                    showCancelButton: true,
                                    showLoaderOnConfirm: true,
                                    confirmButtonText: "Ya, Valid!",
                                    cancelButtonText: 'Tidak',
                                    preConfirm: function () {
                                        $.ajax({
                                            url: "<?php echo base_url() . "admin/dashboard/valid/"; ?>" + $id,
                                            type: "POST"
                                        })
                                                .done(function (data) {
                                                    swal.fire({
                                                        title: "Valid",
                                                        text: "Data Telah Valid!",
                                                        type: "success",
                                                        preConfirm: function () {
                                                            location.href = '<?php echo base_url();?>'+"admin/dashboard/";
                                                        }
                                                    });
                                                })
                                                .error(function (data) {
                                                    swal.fire("Oops", "No connection!", "error");
                                                });
                                    }
                                });
                            }

                            function hapus($id)
                            {
                                swal.fire({
                                    title: "Anda Yakin?",
                                    text: "Anda Yakin Ingin Menghapus Pendaftar Ini?",
                                    type: "warning",
                                    showCancelButton: true,
                                    showLoaderOnConfirm: true,
                                    confirmButtonText: "Ya, Hapus!",
                                    cancelButtonText: 'Tidak',
                                    preConfirm: function () {
                                        $.ajax({
                                        url: "<?php echo base_url() . "admin/dashboard/delete/"; ?>" + $id,
                                        type: "POST"
                                    })
                                            .done(function (data) {
                                                swal.fire({
                                                    title: "Hapus",
                                                    text: "Data Telah Terhapus!",
                                                    type: "success"
                                                }, function () {
                                                    location.href = '<?php echo base_url();?>'+"admin/dashboard/";
                                                });
                                            })
                                            .error(function (data) {
                                                swal.fire("Oops", "No connection!", "error");
                                            });
                                    }
                                });
                            }

</script>

</body>
</html>
