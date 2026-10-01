
    

    <!-- Page -->
    <div class="page">
    
      <div class="page-content container-fluid">
        <div class="row"  data-by-row="true">
<div class="col-xl-4 col-md-8">
    <div class="panel">
        <div class="card">
                <div class="card-header white bg-red-600 p-30 clearfix">
                    <a class="avatar avatar-100 float-left mr-20" href="javascript:void(0)">
                        <img src="<?php echo base_url("assets/assets/images/logostik.png");?>" alt=""></a>
                        <div class="float-left">
                            <div class="font-size-20 mb-15"><?php if(isset($formulir['form_nama'])){echo $formulir['form_nama'];}?></div>
                            
                            
                            
                            
                        </div>
                    </div>
            </div>
    </div>
</div>     
<div class="col-xl-8 col-md-16">
        <?php if (!empty($perlu_migrasi)): ?>
        <div class="alert alert-warning">
            <i class="icon md-alert-circle-o" aria-hidden="true"></i>
            Sebagian pertanyaan masih memakai kolom lingkup lama (<code>dtform_lingkup</code>) dan belum
            dipecah menjadi butir-butir lingkup.
            <a href="<?php echo base_url('admin/migrasi'); ?>" class="alert-link">Jalankan migrasi lingkup</a>
            agar daftar tilik mengikuti struktur baru.
        </div>
        <?php endif; ?>
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

    <?php /* Templat satu baris butir lingkup (dipakai assets/app/admin/detailform.js) */ ?>
    <div id="lingkup_templat" style="display:none;">
        <div class="lingkup-baris">
            <input type="hidden" name="lingkup_id[]" value="0">
            <textarea class="form-control" name="lingkup_isi[]" rows="2"
                placeholder="Tulis butir lingkup, mis. dokumen/bukti yang diminta"></textarea>
            <button type="button" class="btn btn-sm btn-danger lingkup-hapus" title="Hapus butir ini">
                <i class="icon md-close" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <style>
        .lingkup-baris { display: flex; align-items: flex-start; margin-bottom: 8px; }
        .lingkup-baris textarea { flex: 1 1 auto; }
        .lingkup-baris .lingkup-hapus { margin-left: 8px; flex: 0 0 auto; }
    </style>

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
                    <div class="col-md-12">
                      <h4 class="example-title">Butir Lingkup Pertanyaan</h4>
                      <p class="text-muted">Satu pertanyaan boleh punya banyak butir lingkup. Tiap butir
                        menjadi satu baris daftar tilik saat audit.</p>
                      <div class="lingkup-daftar-baris" id="lingkup_add_daftar"></div>
                      <button type="button" class="btn btn-sm btn-primary lingkup-tambah" data-target="lingkup_add_daftar">
                        <i class="icon md-plus" aria-hidden="true"></i>Tambah Butir
                      </button>
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
                    <div class="col-md-12">
                      <h4 class="example-title">Butir Lingkup Pertanyaan</h4>
                      <div class="lingkup-daftar-baris" id="lingkup_edit_daftar"></div>
                      <button type="button" class="btn btn-sm btn-primary lingkup-tambah" data-target="lingkup_edit_daftar">
                        <i class="icon md-plus" aria-hidden="true"></i>Tambah Butir
                      </button>
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

    

    
