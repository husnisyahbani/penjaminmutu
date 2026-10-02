
    

    <!-- Page -->
    <div class="page">
    
      <div class="page-content container-fluid">
      

        <div class="panel">
            <header class="panel-heading panel-heading-filter">
                <div class="panel-heading-isi">
                    <h3 class="panel-title">Formulir</h3>
                    <div class="panel-aksi">
                        <?php if (!empty($list_periode)): ?>
                        <div class="filter-kotak">
                            <label for="filter_periode"><i class="icon md-calendar" aria-hidden="true"></i>Periode</label>
                            <select class="form-control" id="filter_periode">
                                <option value="">Semua Periode</option>
                                <?php foreach ($list_periode as $p):
                                    $label = html_escape($p['periode_tahun'])
                                        . ' &middot; ' . date('d-m-Y', strtotime($p['periode_mulai']))
                                        . ' s.d. ' . date('d-m-Y', strtotime($p['periode_selesai']))
                                        . (!empty($p['periode_aktif']) ? ' &middot; Aktif' : '');
                                ?><option value="<?php echo (int) $p['periode_id']; ?>" <?php echo ($periode_aktif == $p['periode_id']) ? 'selected' : ''; ?>><?php echo $label; ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="panel-aksi__tombol">
                            <button type="button" class="btn btn-sm btn-success" id="tambah">
                                <i class="icon md-plus" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <table class="table table-hover dataTable table-striped w-full" id="formulir">
                    <thead>
                        <tr>
                            <th width="20px">No</th>
                            <th>Nama Formulir</th>
                            <th>Kode</th>
                            <th>Deskripsi</th>
                            <th width="1%">Periode</th>
                            <th width="200px">Tanggal</th>
                            <th width="1%" class="tabel-aksi-sel">Aksi</th>
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
    id="formulirAddModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formaddformulir" class="modal-content"  method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Tambah Formulir</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                      <h4 class="example-title">Judul Formulir</h4>
                        <input type="text" class="form-control" id="form_nama" name="form_nama" placeholder="Masukkan Judul Formulir" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Kode Formulir</h4>
                        <input type="text" class="form-control" id="form_kode" name="form_kode" placeholder="Masukkan Kode Formulir" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Deskripsi</h4>
                        <textarea id="isi_artikel" class="editor" name="form_deskripsi"></textarea>
                    </div>
                    <?php if (!empty($list_periode)): ?>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Periode</h4>
                        <select class="form-control" id="form_periode" name="periode_id">
                            <option value="">Tanpa Periode</option>
                            <?php foreach ($list_periode as $p): ?>
                            <option value="<?php echo (int) $p['periode_id']; ?>" <?php echo ($periode_aktif == $p['periode_id']) ? 'selected' : ''; ?>>
                                <?php echo html_escape($p['periode_tahun']) . ' (' . date('d-m-Y', strtotime($p['periode_mulai'])) . ' s.d. ' . date('d-m-Y', strtotime($p['periode_selesai'])) . ')'; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" id="submitformulir" name="submitformulir" value="submitformulir">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>


<div
    class="modal fade"
    id="formulirEditModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formeditformulir" class="modal-content"  method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Edit Formulir</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                      <h4 class="example-title">Judul Formulir</h4>
                        <input type="text" class="form-control" id="edit_form_nama" name="form_nama" placeholder="Masukkan Judul Formulir" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Kode Formulir</h4>
                        <input type="text" class="form-control" id="edit_form_kode" name="form_kode" placeholder="Masukkan Kode Formulir" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Deskripsi</h4>
                        <textarea id="edit_isi_artikel" class="editor" name="form_deskripsi"></textarea>
                    </div>
                    <?php if (!empty($list_periode)): ?>
                    <div class="col-md-12 center">
                      <h4 class="example-title">Periode</h4>
                        <select class="form-control" id="edit_form_periode" name="periode_id">
                            <option value="">Tanpa Periode</option>
                            <?php foreach ($list_periode as $p): ?>
                            <option value="<?php echo (int) $p['periode_id']; ?>">
                                <?php echo html_escape($p['periode_tahun']) . ' (' . date('d-m-Y', strtotime($p['periode_mulai'])) . ' s.d. ' . date('d-m-Y', strtotime($p['periode_selesai'])) . ')'; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <input type="hidden" id="form_id" name="form_id" />
                </div>
            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" name="submitformulir" value="submitformulir">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>

    

    
