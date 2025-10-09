<?php
include 'header.php';
?>

<!-- Page -->
<div class="page">

    

    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-lg-12">
                
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">DATA WISUDAWAN/I STIK SITI KHADIJAH PALEMBANG </h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" id="exampleStandardForm" autocomplete="off" action="<?php if(isset($id)){
                        echo base_url().'admin/dashboard/edit/'.$id;
                        }else{
                        echo base_url().'admin/dashboard/simpan';
                        }?>" method="post" enctype="multipart/form-data">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Program Studi Yang Dipilih</label>
                                <div class="col-md-9">
                                    <div class="radio-custom radio-default">
                                        <input type="radio" id="prodid3kebidanan" name="prodi" value="D3 - Kebidanan" <?php if(isset($result[0]['prodi']) && $result[0]['prodi'] === 'D3 - Kebidanan')echo "checked";?>/>
                                        <label for="prodid3kebidanan">D3 - Kebidanan</label>
                                    </div>
                                    <div class="radio-custom radio-default">
                                        <input type="radio" id="prodid3keperawatan" name="prodi" value="D3 - Keperawatan" <?php if(isset($result[0]['prodi']) && $result[0]['prodi'] === 'D3 - Keperawatan')echo "checked";?> />
                                        <label for="prodid3keperawatan">D3 - Keperawatan</label>
                                    </div>
                                    <div class="radio-custom radio-default">
                                        <input type="radio" id="prodis1keperawatan" name="prodi" value="S1 - Keperawatan" <?php if(isset($result[0]['prodi']) && $result[0]['prodi'] === 'S1 - Keperawatan')echo "checked";?>/>
                                        <label for="prodis1keperawatan">S1 - Keperawatan</label>
                                    </div>
                                    <div class="radio-custom radio-default">
                                        <input type="radio" id="prodis1farmasi" name="prodi" value="S1 - Farmasi" <?php if(isset($result[0]['prodi']) && $result[0]['prodi'] === 'S1 - Farmasi')echo "checked";?>/>
                                        <label for="inputBasicFemale">S1 - Farmasi</label>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">NIK</label>
                                <div class="col-md-9">
                                    <input type="number" class="form-control" name="nik" value="<?php if(isset($result[0]['nik']))echo $result[0]['nik'];?>"/>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">NIM</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="nim" value="<?php if(isset($result[0]['nim']))echo $result[0]['nim'];?>"/>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nama Lengkap</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="nama_lengkap" value="<?php if(isset($result[0]['nama_lengkap']))echo $result[0]['nama_lengkap'];?>"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Tempat Lahir</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="tempat_lahir" value="<?php if(isset($result[0]['tempat_lahir']))echo $result[0]['tempat_lahir'];?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Tanggal Lahir (DD-MM-YYYY)</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="tanggal_lahir" data-plugin="datepicker" data-date-format="dd-mm-yyyy" value="<?php if(isset($result[0]['tanggal_lahir']))echo $result[0]['tanggal_lahir'];?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Jenis Kelamin</label>
                                <div class="col-md-9">
                                    <div class="radio-custom radio-default radio-inline">
                                        <input type="radio" id="inputBasicMale" name="jenis_kelamin" value="Laki-Laki"   <?php if(isset($result[0]['jenis_kelamin']) && $result[0]['jenis_kelamin'] === 'Laki-Laki')echo "checked";?>/>
                                        <label for="inputBasicMale">Laki - Laki</label>
                                    </div>
                                    <div class="radio-custom radio-default radio-inline">
                                        <input type="radio" id="inputBasicFemale" name="jenis_kelamin" value="Perempuan" <?php if(isset($result[0]['jenis_kelamin']) && $result[0]['jenis_kelamin'] === 'Perempuan')echo "checked";?>/>
                                        <label for="inputBasicFemale">Perempuan</label>
                                    </div>
                                </div>
                            </div>

                            

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Agama</label>
                                <div class="col-md-9">
                                    <div class="radio-custom radio-default">
                                        <input type="radio" id="agamaislam" name="agama" value="Islam" <?php if(isset($result[0]['agama']) && $result[0]['agama'] === 'Islam')echo "checked";?>/>
                                        <label for="agamaislam">Islam</label>
                                    </div>
                                    <div class="radio-custom radio-default">
                                        <input type="radio" id="agamakatolik" name="agama" value="Katolik" <?php if(isset($result[0]['agama']) && $result[0]['agama'] === 'Katolik')echo "checked";?>/>
                                        <label for="agamakatolik">Katolik</label>
                                    </div>
                                    <div class="radio-custom radio-default">
                                        <input type="radio" id="agamaprotestan" name="agama" value="Protestan" <?php if(isset($result[0]['agama']) && $result[0]['agama'] === 'Protestan')echo "checked";?>/>
                                        <label for="agamaprotestan">Protestan</label>
                                    </div>
                                    <div class="radio-custom radio-default">
                                        <input type="radio" id="agamahindu" name="agama" value="Hindu" <?php if(isset($result[0]['agama']) && $result[0]['agama'] === 'Hindu')echo "checked";?>/>
                                        <label for="agamahindu">Hindu</label>
                                    </div>
                                    <div class="radio-custom radio-default">
                                        <input type="radio" id="agamabudha" name="agama" value="Budha" <?php if(isset($result[0]['agama']) && $result[0]['agama'] === 'Budha')echo "checked";?>/>
                                        <label for="agamabudha">Budha</label>
                                    </div>

                                </div>
                            </div>

                            

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nomor Telp/HP</label>
                                <div class="col-md-9">
                                    <input type="number" class="form-control" id="no_telp" name="no_telp" value="<?php if(isset($result[0]['no_telp']))echo $result[0]['no_telp'];?>"/>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">email</label>
                                <div class="col-md-9">
                                    <input type="email" class="form-control" id="email" name="email" value="<?php if(isset($result[0]['email']))echo $result[0]['email'];?>"/>
                                </div>
                            </div>


                            

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Alamat Lengkap</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" name="alamat_lengkap_mahasiswa" rows="5"><?php if(isset($result[0]['alamat_lengkap_mahasiswa']))echo $result[0]['alamat_lengkap_mahasiswa'];?></textarea>
                                </div>
                            </div>


                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="validateButton2">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- End Panel Wizard Form Container -->
            </div>
        </div>
    </div>
</div>
<!-- End Page -->

<!-- Footer -->
<footer class="site-footer">
    <div class="site-footer-legal">© 2019 <a href="http://husnisyahbani.blogspot.com">M HUSNI SYAHBANI</a></div>
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

<script src="<?php echo asset_url() . 'global/js/Plugin/animate-list.js'; ?>"></script>
<script src="<?php echo asset_url() . 'global/js/Plugin/panel.js'; ?>"></script>
<script src="<?php echo asset_url() . 'assets/examples/js/layouts/panel-transition.js'; ?>"></script>


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
                prodi: {
                    validators: {
                        notEmpty: {
                            message: 'Wajib Diisi'
                        }
                    }
                },
                nim: {
                    validators: {
                        notEmpty: {
                            message: 'Wajib Diisi'
                        }
                    }
                },
                nama_lengkap: {
                    validators: {
                        notEmpty: {
                            message: 'Wajib Diisi'
                        }
                    }
                },
                tempat_lahir: {
                    validators: {
                        notEmpty: {
                            message: 'Wajib Diisi'
                        }
                    }
                },
                tanggal_lahir: {
                    validators: {
                        notEmpty: {
                            message: 'Tanggal Wajib Diisi'
                        }
                    }
                },
                jenis_kelamin: {
                    validators: {
                        notEmpty: {
                            message: 'Wajib Diisi'
                        }
                    }
                },
                nik: {
                    validators: {
                        notEmpty: {
                            message: 'Wajib Diisi'
                        }
                    }
                },
                agama: {
                    validators: {
                        notEmpty: {
                            message: 'Wajib Diisi'
                        }
                    }
                },
                no_telp: {
                    validators: {
                        notEmpty: {
                            message: 'Wajib Diisi'
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'Wajib Diisi'
                        }
                    }
                },
                alamat_lengkap_mahasiswa: {
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
