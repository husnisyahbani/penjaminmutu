<?php
/**
 * Daftar Periode Audit (menu Audit > Periode).
 *
 * Variabel dari pengontrol:
 *   $terpasang : apakah tabel periode + kolom relasi sudah siap
 *   $tahun     : pilihan tahun untuk formulir
 */
?>
<div class="page">
    <div class="page-content container-fluid">

        <?php if (!$terpasang): ?>
        <div class="alert alert-warning">
            <i class="icon md-alert-circle-o" aria-hidden="true"></i>
            Tabel periode belum siap (tabel <code>mutu_periode</code> dan kolom
            <code>mutu_audit.periode_id</code> belum ada). Klik tombol di bawah
            untuk menyiapkannya, atau impor berkas
            <code>database/periode_audit.sql</code>.
            <div style="margin-top:10px;">
                <button type="button" class="btn btn-warning" id="pasangTabel">
                    <i class="icon md-calendar" aria-hidden="true"></i>Buat Tabel Periode
                </button>
            </div>
        </div>
        <?php endif; ?>

        <div class="row" data-by-row="true">

            <div class="col-xl-12 col-md-24">

                <div class="panel">
                    <header class="panel-heading">
                        <h3 class="panel-title">Periode Audit</h3>
                        <div class="panel-actions panel-actions-keep">
                            <button type="button" class="btn btn-success" id="tambahperiode" <?php echo $terpasang ? '' : 'disabled'; ?>>
                                <i class="icon md-plus" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </header>
                    <div class="panel-body">
                        <p class="text-muted">
                            Periode yang ditandai <span class="badge badge-success">Aktif</span> dipakai sebagai
                            filter bawaan pada halaman <a href="<?php echo base_url('admin/daftaraudit'); ?>">Daftar Audit</a>.
                            Bila tidak ada periode aktif, Daftar Audit menampilkan seluruh data.
                        </p>
                        <table class="table table-hover dataTable w-full" id="periode">
                            <thead>
                                <tr>
                                    <th width="1%">No</th>
                                    <th>Tahun</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Berakhir</th>
                                    <th width="1%" class="tabel-aksi-sel">Status</th>
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
    </div>
</div>

<!-- Tambah periode -->
<div class="modal fade" id="periodeAddModal" aria-hidden="true" aria-labelledby="periodeAddLabel" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formaddperiode" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="periodeAddLabel">Tambah Periode</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="example-title">Tahun</h4>
                        <select class="form-control" name="periode_tahun" id="add_periode_tahun"
                                data-fv-notempty="true" data-fv-notempty-message="Wajib Dipilih">
                            <?php foreach ($tahun as $t): ?>
                                <option value="<?php echo $t; ?>" <?php echo ($t == date('Y')) ? 'selected' : ''; ?>><?php echo $t; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <h4 class="example-title">Tanggal Mulai</h4>
                        <input type="date" class="form-control" id="add_periode_mulai" name="periode_mulai"
                               data-fv-notempty="true" data-fv-notempty-message="Wajib Diisi" />
                    </div>
                    <div class="col-md-12">
                        <h4 class="example-title">Tanggal Berakhir</h4>
                        <input type="date" class="form-control" id="add_periode_selesai" name="periode_selesai"
                               data-fv-notempty="true" data-fv-notempty-message="Wajib Diisi" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="submitperiode" value="submitperiode">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit periode -->
<div class="modal fade" id="periodeEditModal" aria-hidden="true" aria-labelledby="periodeEditLabel" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formeditperiode" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="periodeEditLabel">Ubah Periode</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="example-title">Tahun</h4>
                        <select class="form-control" name="periode_tahun" id="edit_periode_tahun"
                                data-fv-notempty="true" data-fv-notempty-message="Wajib Dipilih">
                            <?php foreach ($tahun as $t): ?>
                                <option value="<?php echo $t; ?>"><?php echo $t; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <h4 class="example-title">Tanggal Mulai</h4>
                        <input type="date" class="form-control" id="edit_periode_mulai" name="periode_mulai"
                               data-fv-notempty="true" data-fv-notempty-message="Wajib Diisi" />
                    </div>
                    <div class="col-md-12">
                        <h4 class="example-title">Tanggal Berakhir</h4>
                        <input type="date" class="form-control" id="edit_periode_selesai" name="periode_selesai"
                               data-fv-notempty="true" data-fv-notempty-message="Wajib Diisi" />
                    </div>
                    <input type="hidden" id="periode_id" name="periode_id" />
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="submiteditperiode" value="submiteditperiode">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
