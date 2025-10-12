


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
                        <form class="form-horizontal" id="formaddakun" autocomplete="off" action="<?php echo base_url() . 'admin/akun/edit?id='.$result[0]['users_id'];?>" method="post" enctype="multipart/form-data">

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nama</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="nama" value="<?php if (isset($result[0]['nama'])) echo $result[0]['nama']; ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Unit Kerja</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="unitkerja" value="<?php if (isset($result[0]['unitkerja'])) echo $result[0]['unitkerja']; ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Username</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="username" value="<?php if (isset($result[0]['username'])) echo $result[0]['username']; ?>"/>
                                </div>
                            </div>

                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Role</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="role" id="role">
                                        <option value="">-- Pilih Peran --</option>
                                        <option value="LPM" <?php if (isset($result[0]['role']) && $result[0]['role'] === "PPM") {echo "selected";} ?>>PPM</option>
                                        <option value="AUDITOR" <?php if (isset($result[0]['role']) && $result[0]['role'] === "AUDITOR") {echo "selected";} ?>>AUDITOR</option>
                                        <option value="AUDITEE" <?php if (isset($result[0]['role']) && $result[0]['role'] === "AUDITEE") {echo "selected";} ?>>AUDITEE</option>
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






