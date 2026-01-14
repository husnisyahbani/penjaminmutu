
    

    <!-- Page -->
    <div class="page">
    
      <div class="page-content container-fluid">
      

        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title"><?=$title;?></h3> 
                <div class="panel-actions panel-actions-keep">
                    <button type="button" class="btn btn-sm btn-icon btn-success" id="tambahsk">
                                <i class="icon md-plus" aria-hidden="true"></i>Tambah
                    </button>
                    
                </div>
            </header>
            <div class="panel-body">
                <table class="table table-hover dataTable table-striped w-full" id="sk">
                    <thead>
                        <tr>
                            <th width="20px">No</th>
                            <th>Judul</th>
                            <th>File</th>
                            <th width="200px">Tanggal</th>
                            <th width="150px">Aksi</th>
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
    id="skAddModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formaddsk" class="modal-content"  method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Tambah SK</h4>
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
                        <input type="text" class="form-control" id="sk_judul" name="sk_judul" placeholder="Masukkan Judul" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    
                    
                </div>
            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" name="submitsk" value="submitsk">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>


<div
    class="modal fade"
    id="skEditModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formeditsk" class="modal-content"  method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Edit SK</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                      <h4 class="example-title">File</h4>
                        <input type="file" id="edit_file" name="upload_file" data-plugin="dropify"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Judul</h4>
                        <input type="text" class="form-control" id="edit_sk_judul" name="sk_judul" placeholder="Masukkan Judul" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    
                    
                    <input type="hidden" id="sk_id" name="sk_id" />
                </div>
            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" name="submitsk" value="submitsk">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>

    

    
