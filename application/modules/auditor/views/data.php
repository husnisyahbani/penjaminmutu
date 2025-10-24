
    

    <!-- Page -->
    <div class="page">
    
      <div class="page-content container-fluid">
      

        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title"><?=$title;?></h3> 
                <div class="panel-actions panel-actions-keep">
                    
                    
                </div>
            </header>
            <div class="panel-body">
                <table class="table table-hover dataTable table-striped w-full" id="data">
                    <thead>
                        <tr>
                            <th width="20px">No</th>
                            <th>Judul</th>
                            <th>Keterangan</th>
                            <th>File</th>
                            <th>Bagian</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>

                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>

        
          
          
    
    </div>
    </div>
    <!-- End Page -->

    <div
    class="modal fade"
    id="dataAddModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formadddata" class="modal-content"  method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Tambah Data</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                      <h4 class="example-title">File</h4>
                        <input type="file" id="add_file" name="upload_file" data-plugin="dropify" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Judul</h4>
                        <input type="text" class="form-control" id="data_uraian" name="data_uraian" placeholder="Masukkan Judul" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Keterangan</h4>
                        <input type="text" class="form-control" id="data_keterangan" name="data_keterangan" placeholder="Masukkan Keterangan" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Bidang</h4>
                        <select class="form-control" name="data_kategori" id="data_kategori" data-fv-notempty="true"
                                    data-fv-notempty-message="Wajib Di isi" >                                 
                            <option value="">-- Pilih Bidang --</option>
                            <option value="PENETAPAN">PENETAPAN</option>
                            <option value="PELAKSANAAN">PELAKSANAAN</option>
                            <option value="EVALUASI">EVALUASI</option>
                            <option value="PENGENDALIAN">PENGENDALIAN</option>
                            <option value="PENINGKATAN">PENINGKATAN</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" name="submitdata" value="submitdata">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>


<div
    class="modal fade"
    id="dataEditModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formeditdata" class="modal-content"  method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Edit Formulir</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                      <h4 class="example-title">File</h4>
                        <input type="file" id="edit_file" name="upload_file" data-plugin="dropify"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Judul</h4>
                        <input type="text" class="form-control" id="edit_data_uraian" name="data_uraian" placeholder="Masukkan Judul" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Keterangan</h4>
                        <input type="text" class="form-control" id="edit_data_keterangan" name="data_keterangan" placeholder="Masukkan Keterangan" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Bidang</h4>
                        <select class="form-control" name="data_kategori" id="edit_data_kategori" data-fv-notempty="true"
                                    data-fv-notempty-message="Wajib Di isi" >                                 
                            <option value="">-- Pilih Bidang --</option>
                            <option value="PENETAPAN">PENETAPAN</option>
                            <option value="PELAKSANAAN">PELAKSANAAN</option>
                            <option value="EVALUASI">EVALUASI</option>
                            <option value="PENGENDALIAN">PENGENDALIAN</option>
                            <option value="PENINGKATAN">PENINGKATAN</option>
                        </select>
                    </div>
                    <input type="hidden" id="data_id" name="data_id" />
                </div>
            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" name="submitdata" value="submitdata">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>

    

    
