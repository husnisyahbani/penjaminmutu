


<!-- Page -->
<div class="page">
    <div class="page-content container-fluid">

        <div class="row">
            <div class="col-lg-12">

                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tambah Pengumuman </h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" id="formaddpengumuman" autocomplete="off" action="<?php echo base_url() . 'admin/pengumuman/tambah';?>" method="post" enctype="multipart/form-data">

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Pengumuman</label>
                                <div class="col-md-9">
                                    <input type="file" id="input-file-now" name="upload_file" data-plugin="dropify"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Judul</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="pengumuman_judul" name="pengumuman_judul" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Deskripsi</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="pengumuman_deskripsi" name="pengumuman_deskripsi" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"/>
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Isi </label>
                                <div class="col-md-9">
                                   <textarea id="pengumuman_isi" class="editor" name="pengumuman_isi"></textarea>
                                </div>
                            </div>

                            


                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="validateButton" name="submitpengumuman">Submit</button>
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






