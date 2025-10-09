
    

    <!-- Page -->
    <div class="page">
    <div class="page-header">
        
        <div class="page-header-actions">
          <button type="button" class="btn btn-sm btn-icon btn-success" data-toggle="tooltip"
            data-original-title="Tambah" id="tambah">
            <i class="icon md-plus" aria-hidden="true"></i>Tambah
          </button>
          
        </div>
      </div>
      <div class="page-content container-fluid">
      

        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title"><?=$title?></h3> 
                <div class="panel-actions panel-actions-keep">
                    <div class="row">
                        
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <table class="table table-hover dataTable table-striped w-full" id="dtform">
                    <thead>
                        <tr>
                            <th width="20px">No</th>
                            <th>Tujuan</th>
                            <th>Pertanyaan</th>
                            <th>Lingkup</th>
                            <th width="200px">Tanggal</th>
                            <th width="100px">Aksi</th>
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
    id="dtformAddModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formadddtform" class="modal-content"  method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Tambah</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                      <h4 class="example-title">Tujuan</h4>
                        <input type="text" class="form-control" id="dtform_tujuan" name="dtform_tujuan" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Pertanyaan</h4>
                        <textarea class="editor" id="dtform_pertanyaan" name="dtform_pertanyaan" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"></textarea>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Lingkup Pertanyaan</h4>
                        <textarea class="editor" id="dtform_lingkup" name="dtform_lingkup" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"></textarea>
                    </div>
                    <input type="hidden" id="form_id" name="form_id" value="<?php echo $form_id;?>"/>
                </div>
            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" id="submitdtform" name="submitdtform" value="submitdtform">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>


<div
    class="modal fade"
    id="dtformEditModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formeditdtform" class="modal-content"  method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Edit</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 center">
                      <h4 class="example-title">Tujuan</h4>
                        <input type="text" class="form-control" id="edit_dtform_tujuan" name="dtform_tujuan" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Pertanyaan</h4>
                        <textarea class="editor" id="edit_dtform_pertanyaan" name="dtform_pertanyaan" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"></textarea>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Lingkup Pertanyaan</h4>
                        <textarea class="editor" id="edit_dtform_lingkup" name="dtform_lingkup" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"></textarea>
                    </div>
                    <input type="hidden" id="edit_form_id" name="form_id" value="<?php echo $form_id;?>"/>
                    <input type="hidden" id="dtform_id" name="dtform_id" />
                </div>
            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" name="submitdtform" value="submitdtform">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>

    

    
