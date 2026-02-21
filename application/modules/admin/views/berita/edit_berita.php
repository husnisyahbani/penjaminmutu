


<!-- Page -->
<div class="page">
    <div class="page-content container-fluid">

        <div class="row">
            <div class="col-lg-12">

                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">Edit Berita </h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" id="formeditberita" autocomplete="off" action="<?php if(isset($result['berita_id'])) echo base_url() .$module. '/berita/edit/'.$result['berita_id'];?>" method="post" enctype="multipart/form-data">

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Berita</label>
                                <div class="col-md-9">
                                    <input type="file" id="input-file-now" name="upload_file" data-plugin="dropify" data-default-file="<?php if (isset($result['berita_file'])) {echo base_url ().$result['berita_file'];} ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Tanggal</label>
                                <div class="col-md-9">
                                    <input type="date" class="form-control" id="berita_create" name="berita_create" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi" value="<?php if (isset($result['berita_create'])) echo $result['berita_create']; ?>"/>
                                    
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Judul</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="berita_judul" name="berita_judul" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi" value="<?php if (isset($result['berita_judul'])) echo $result['berita_judul']; ?>"/>
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Deskripsi</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="berita_deskripsi" name="berita_deskripsi" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi" value="<?php if (isset($result['berita_deskripsi'])) echo $result['berita_deskripsi']; ?>"/>
                                    
                                </div>
                            </div>

                            

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Isi</label>
                                <div class="col-md-9">
                                     <textarea id="berita_isi" class="editor" name="berita_isi"><?php if (isset($result['berita_isi'])) echo $result['berita_isi']; ?></textarea>
                                </div>
                            </div>


                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="validateButton" name="submitberita" value="submit">Submit</button>
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






