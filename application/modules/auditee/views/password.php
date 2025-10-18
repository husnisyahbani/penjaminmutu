


<!-- Page -->
<div class="page">
    <div class="page-content container-fluid">

        <div class="row">
            <div class="col-lg-12">

                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">Ubah Password </h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" id="formpassword" autocomplete="off" action="<?php echo base_url() . $module.'/dashboard/password';?>" method="post" enctype="multipart/form-data">

                            

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Password Lama</label>
                                <div class="col-md-9">
                                    <input type="password" class="form-control" name="passlama" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Password</label>
                                <div class="col-md-9">
                                    <input type="password" class="form-control" name="password"  data-fv-identical="true" data-fv-identical-field="repassword"
                                           data-fv-identical-message="Password Tidak Sama" />
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Password</label>
                                <div class="col-md-9">
                                    <input type="password" class="form-control" name="repassword"  data-fv-identical="true" data-fv-identical-field="password"
                                           data-fv-identical-message="Password Tidak Sama" />
                                </div>
                            </div>
                            
                              

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="validateButton" name="submitpass">Submit</button>
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






