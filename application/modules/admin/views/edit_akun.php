


<!-- Page -->
<div class="page">
    <div class="page-content container-fluid">

        <div class="row">
            <div class="col-lg-12">

                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">Akun </h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" id="formaddakun" autocomplete="off" action="<?php echo base_url() . 'admin/akun/edit?id='.$result['users_id'];?>" method="post" enctype="multipart/form-data">

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nama</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="nama" value="<?php if (isset($result['nama'])) echo $result['nama']; ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Unit Kerja</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="unitkerja" value="<?php if (isset($result['unitkerja'])) echo $result['unitkerja']; ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Username</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="username" value="<?php if (isset($result['username'])) echo $result['username']; ?>"/>
                                </div>
                            </div>

                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Role</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="role" id="role">
                                        <option value="">-- Pilih Peran --</option>
                                        <option value="PPM" <?php if (isset($result['role']) && $result['role'] === "PPM") {echo "selected";} ?>>PPM</option>
                                        <option value="AUDITOR" <?php if (isset($result['role']) && $result['role'] === "AUDITOR") {echo "selected";} ?>>AUDITOR</option>
                                        <option value="AUDITEE" <?php if (isset($result['role']) && $result['role'] === "AUDITEE") {echo "selected";} ?>>AUDITEE</option>
                                        </select>
                                </div>
                            </div>   

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="validateButton" name="submitakun">Submit</button>
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






