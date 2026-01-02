


<!-- Page -->
<div class="page">
    <div class="page-content container-fluid">

        <div class="row">
            <div class="col-lg-12">

                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">Edit Pengumuman </h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" id="formeditpengumuman" autocomplete="off" action="<?php if(isset($result['pengumuman_id'])) echo base_url() .$module. '/pengumuman/edit/'.$result['pengumuman_id'];?>" method="post" enctype="multipart/form-data">

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Pengumuman</label>
                                <div class="col-md-9">
                                    <input type="file" id="input-file-now" name="upload_file" data-plugin="dropify" data-default-file="<?php if (isset($result['pengumuman_file'])) {echo base_url ().$result['pengumuman_file'];} ?>"/>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Judul</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="pengumuman_judul" name="pengumuman_judul" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi" value="<?php if (isset($result['pengumuman_judul'])) echo $result['pengumuman_judul']; ?>"/>
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Deskripsi</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="pengumuman_deskripsi" name="pengumuman_deskripsi" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi" value="<?php if (isset($result['pengumuman_deskripsi'])) echo $result['pengumuman_deskripsi']; ?>"/>
                                    
                                </div>
                            </div>

                            

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Isi</label>
                                <div class="col-md-9">
                                     <textarea id="pengumuman_isi" class="editor" name="pengumuman_isi"><?php if (isset($result['pengumuman_isi'])) echo $result['pengumuman_isi']; ?></textarea>
                                </div>
                            </div>


                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="validateButton" name="submitpengumuman" value="submit">Submit</button>
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






