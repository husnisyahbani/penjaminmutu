
    

    <!-- Page -->
    <div class="page">
    
      <div class="page-content container-fluid">
        <div class="row"  data-by-row="true">
<div class="col-xl-4 col-md-8">
    <div class="panel">
        <div class="card">
                <div class="card-header white bg-red-600 p-30 clearfix">
                    <a class="avatar avatar-100 float-left mr-20" href="javascript:void(0)">
                        <img src="<?php echo base_url("assets/assets/images/sijamu.png");?>" alt=""></a>
                        <div class="float-left">
                            <div class="font-size-20 mb-15"><?php if(isset($formulir['form_nama'])){echo $formulir['form_nama'];}?></div>
                            
                            
                            
                            
                        </div>
                    </div>
            </div>
    </div>
</div>     
<div class="col-xl-8 col-md-16">
        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title"><?=$title?></h3> 
                <div class="panel-actions panel-actions-keep">
                    <div class="row">
                        <button type="button" class="btn btn-sm btn-icon btn-success" id="tambah">
                                <i class="icon md-plus" aria-hidden="true"></i>Tambah
                            </button>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <table class="table table-hover dataTable table-striped w-full" id="dtform">
                    <thead>
                        <tr>
                            <th width="20px">No</th>
                            <th>Pertanyaan</th>
                            <th>Lingkup</th>
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

    

    
