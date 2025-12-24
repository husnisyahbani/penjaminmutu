


<!-- Page -->
<div class="page">
    <div class="page-content container-fluid">

        <div class="row">
            <div class="col-lg-12">

                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tambah Berita </h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" id="formaddberita" autocomplete="off" action="<?php echo base_url() . 'admin/data/tambahdata';?>" method="post" enctype="multipart/form-data">

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Berita</label>
                                <div class="col-md-9">
                                    <input type="file" id="input-file-now" name="upload_file" data-plugin="dropify"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Judul</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="berita_judul" name="berita_judul" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Deskripsi</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="berita_deskripsi" name="berita_deskripsi" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"/>
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Isi </label>
                                <div class="col-md-9">
                                   <textarea id="berita_isi" class="editor" name="berita_isi"></textarea>
                                </div>
                            </div>

                            


                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="validateButton" name="submitberita">Submit</button>
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






