


<!-- Page -->
<div class="page">
    <div class="page-content container-fluid">

        <div class="row">
            <div class="col-lg-12">

                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tambah Dokument </h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" id="formadddata" autocomplete="off" action="<?php echo base_url() . 'admin/data/tambahdata';?>" method="post" enctype="multipart/form-data">

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Dokument</label>
                                <div class="col-md-9">
                                    <input type="file" id="input-file-now" name="upload_file" data-plugin="dropify"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Judul</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id="data_keterangan" name="data_keterangan" rows="6" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"></textarea>
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Uraian</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id="data_uraian" name="data_uraian" rows="6" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"></textarea>
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Kategori</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="data_kategori" value="DATABASE" readonly/>
                                </div>
                            </div>

                            


                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="validateButton" name="submitdata">Submit</button>
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






