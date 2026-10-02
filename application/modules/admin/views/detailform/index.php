<?php
/**
 * Kelola formulir audit dengan gaya kursus: topik = pertanyaan, activity = butir
 * Seluruh pertanyaan/lingkup dirender sekaligus supaya bisa dibuka-tutup,
 * dicari, dan dipindahkan urutannya tanpa memuat halaman.
 *
 * Data dari controller: $formulir, $form_id, $topik, $peta_lingkup, $urut_siap,
 * $perlu_migrasi.
 */
$nama_form    = isset($formulir['form_nama']) ? $formulir['form_nama'] : 'Formulir';
$kode_form    = isset($formulir['form_kode']) ? $formulir['form_kode'] : '';
$periode_form = isset($formulir['periode_tahun']) ? $formulir['periode_tahun'] : '';
if (empty($periode_form) && !empty($formulir['periode_id'])) {
    $periode_form = $formulir['periode_id'];
}

$jumlah_pertanyaan = count($topik);
$jumlah_lingkup = 0;
foreach ($peta_lingkup as $daftar) {
    $jumlah_lingkup += count($daftar);
}
?>
    <!-- Page -->
    <div class="page">
      <div class="page-content container-fluid">

        <?php if (!empty($perlu_migrasi)): ?>
        <div class="alert alert-warning alert-dismissible" role="alert">
            Sebagian pertanyaan masih memakai kolom lingkup lama (<code>dtform_lingkup</code>) dan belum
            dipecah menjadi butir-butir lingkup.
            <a href="<?php echo base_url('admin/migrasi'); ?>" class="alert-link">Jalankan migrasi lingkup</a>
        </div>
        <?php endif; ?>

        <?php if (empty($urut_siap)): ?>
        <div class="alert alert-info alert-dismissible" role="alert" id="kotak_urut">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            Urutan pertanyaan belum aktif pada database ini, sehingga tombol naik/turun pertanyaan dinonaktifkan.
            <button type="button" class="btn btn-sm btn-info ml-10" id="pasang_urut">Aktifkan Urutan Pertanyaan</button>
        </div>
        <?php endif; ?>

        <div class="row" data-by-row="true">

          <div class="col-xl-4 col-md-8">
            <div class="panel">
              <div class="card">
                <div class="card-header white bg-red-600 p-30 clearfix">
                  <a class="avatar avatar-100 float-left mr-20" href="javascript:void(0)">
                    <img src="<?php echo base_url("assets/assets/images/logostik.png"); ?>" alt=""></a>
                  <div class="float-left">
                    <div class="font-size-20 mb-15"><?php echo html_escape($nama_form); ?></div>
                  </div>
                </div>
              </div>
              <div class="panel-body">
                <table class="table table-sm table-borderless mb-0">
                  <tr>
                    <th width="40%">Kode</th>
                    <td><?php echo $kode_form !== '' ? html_escape($kode_form) : '<span class="text-muted">-</span>'; ?></td>
                  </tr>
                  <tr>
                    <th>Periode</th>
                    <td>
                      <?php if ($periode_form !== '' && $periode_form !== NULL): ?>
                      <span class="badge badge-info"><?php echo html_escape($periode_form); ?></span>
                      <?php else: ?>
                      <span class="text-muted">Tanpa periode</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <tr>
                    <th>Pertanyaan</th>
                    <td><?php echo (int) $jumlah_pertanyaan; ?> pertanyaan</td>
                  </tr>
                  <tr>
                    <th>Lingkup</th>
                    <td><?php echo (int) $jumlah_lingkup; ?> lingkup</td>
                  </tr>
                </table>
                <a href="<?php echo base_url('admin/formaudit'); ?>" class="btn btn-sm btn-default mt-15">
                  <i class="icon md-undo" aria-hidden="true"></i>Kembali ke daftar formulir
                </a>
              </div>
            </div>
          </div>

          <div class="col-xl-8 col-md-16">
            <div class="panel">
              <header class="panel-heading panel-heading-filter">
                <div class="panel-heading-isi">
                  <h3 class="panel-title">Pertanyaan &amp; Lingkup</h3>
                  <div class="panel-aksi">
                    <div class="filter-kotak">
                      <label for="cari_topik"><i class="icon md-search" aria-hidden="true"></i>Cari</label>
                      <input type="text" class="form-control" id="cari_topik" placeholder="Cari topik atau activity">
                    </div>
                    <div class="panel-aksi__tombol">
                      <button type="button" class="btn btn-sm btn-success" id="tambah_topik">
                        <i class="icon md-plus" aria-hidden="true"></i>Tambah Pertanyaan
                      </button>
                    </div>
                  </div>
                </div>
              </header>
              <div class="panel-body">
                <div class="topik-berkas" id="topik_daftar">
                  <?php foreach ($topik as $i => $t):
                      $butir = isset($peta_lingkup[$t['dtform_id']]) ? $peta_lingkup[$t['dtform_id']] : array();
                      $judul = lingkup_bersihkan($t['dtform_pertanyaan']);
                      if ($judul === '') {
                          $judul = '(topik tanpa pertanyaan)';
                      }
                      $cari_pertanyaan = strtolower($judul);
                  ?>
                  <section class="topik" data-dtform_id="<?php echo (int) $t['dtform_id']; ?>"
                           data-cari="<?php echo html_escape($cari_pertanyaan); ?>">
                    <header class="topik-kepala">
                      <span class="topik-nomor"><?php echo $i + 1; ?></span>
                      <h4 class="topik-judul">
                        <?php echo html_escape($judul); ?>
                        <span class="topik-info">
                          <span class="badge badge-info badge-activity"><?php echo count($butir); ?> lingkup</span>
                        </span>
                      </h4>
                      <i class="icon md-chevron-down topik-panah" aria-hidden="true"></i>
                      <div class="topik-aksi">
                        <button type="button" class="btn btn-sm btn-icon btn-default topik-naik"
                                data-info="Naikkan pertanyaan" <?php echo $i === 0 ? 'disabled' : ''; ?>>
                          <i class="icon md-chevron-up" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-default topik-turun"
                                data-info="Turunkan pertanyaan">
                          <i class="icon md-chevron-down" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-success topik-edit"
                                data-info="Ubah pertanyaan ini" id="<?php echo (int) $t['dtform_id']; ?>">
                          <i class="icon md-edit" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-danger topik-hapus"
                                data-info="Hapus pertanyaan beserta seluruh lingkupnya" id="<?php echo (int) $t['dtform_id']; ?>">
                          <i class="icon md-delete" aria-hidden="true"></i>
                        </button>
                      </div>
                    </header>

                    <div class="aktivitas-daftar">
                      <?php foreach ($butir as $b):
                          $teks = lingkup_bersihkan($b['lingkup_isi']);
                      ?>
                      <div class="aktivitas" data-lingkup_id="<?php echo (int) $b['lingkup_id']; ?>"
                           data-cari="<?php echo html_escape(strtolower($teks)); ?>">
                        <i class="icon md-assignment aktivitas-ikon" aria-hidden="true"></i>
                        <div class="aktivitas-isi">
                          <span class="aktivitas-teks"><?php echo html_escape($teks); ?></span>
                          <div class="aktivitas-editor" style="display:none;">
                            <textarea class="form-control" rows="2" placeholder="Tulis lingkup, mis. dokumen/bukti yang diminta"><?php echo html_escape($teks); ?></textarea>
                            <div class="aktivitas-editor-aksi">
                              <button type="button" class="btn btn-sm btn-primary aktivitas-simpan">Simpan</button>
                              <button type="button" class="btn btn-sm btn-default aktivitas-batal">Batal</button>
                            </div>
                          </div>
                        </div>
                        <div class="aktivitas-aksi">
                          <button type="button" class="btn btn-sm btn-icon btn-default aktivitas-naik"
                                  data-info="Naikkan lingkup">
                            <i class="icon md-chevron-up" aria-hidden="true"></i>
                          </button>
                          <button type="button" class="btn btn-sm btn-icon btn-default aktivitas-turun"
                                  data-info="Turunkan lingkup">
                            <i class="icon md-chevron-down" aria-hidden="true"></i>
                          </button>
                          <button type="button" class="btn btn-sm btn-icon btn-success aktivitas-edit"
                                  data-info="Ubah isi lingkup">
                            <i class="icon md-edit" aria-hidden="true"></i>
                          </button>
                          <button type="button" class="btn btn-sm btn-icon btn-danger aktivitas-hapus"
                                  data-info="Hapus lingkup ini" id="<?php echo (int) $b['lingkup_id']; ?>">
                            <i class="icon md-delete" aria-hidden="true"></i>
                          </button>
                        </div>
                      </div>
                      <?php endforeach; ?>

                      <div class="aktivitas-kosong" style="display:<?php echo empty($butir) ? '' : 'none'; ?>">
                        Belum ada lingkup pada pertanyaan ini.
                      </div>

                      <div class="aktivitas aktivitas-tambah-baris" style="display:none;">
                        <i class="icon md-assignment aktivitas-ikon" aria-hidden="true"></i>
                        <div class="aktivitas-isi">
                          <textarea class="form-control" rows="2" placeholder="Tulis lingkup, mis. dokumen/bukti yang diminta"></textarea>
                          <div class="aktivitas-editor-aksi">
                            <button type="button" class="btn btn-sm btn-primary aktivitas-simpan">Tambah</button>
                            <button type="button" class="btn btn-sm btn-default aktivitas-batal">Batal</button>
                          </div>
                        </div>
                      </div>

                      <button type="button" class="btn btn-sm btn-primary topik-tambah-activity">
                        <i class="icon md-plus" aria-hidden="true"></i>Tambah Lingkup
                      </button>
                    </div>
                  </section>
                  <?php endforeach; ?>
                </div>

                <div class="topik-kosong" id="topik_kosong" style="display:<?php echo empty($topik) ? '' : 'none'; ?>">
                  Belum ada pertanyaan pada formulir ini. Klik <strong>Tambah Pertanyaan</strong> untuk membuat pertanyaan pertama.
                </div>
                <div class="topik-kosong" id="topik_cari_kosong" style="display:none;">
                  Tidak ada pertanyaan atau lingkup yang cocok dengan pencarian.
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    <!-- End Page -->

<div
    class="modal fade"
    id="topikAddModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formaddtopik" class="modal-content" method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Tambah Pertanyaan</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 center">
                      <h4 class="example-title">Pertanyaan</h4>
                        <textarea class="form-control" id="dtform_pertanyaan" name="dtform_pertanyaan" rows="4"
                        data-fv-notempty="true" data-fv-notempty-message="Wajib Diisi"
                        placeholder="Tulis pertanyaan/topik audit"></textarea>
                    </div>
                    <input type="hidden" id="form_id" name="form_id" value="<?php echo (int) $form_id; ?>"/>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <button type="submit" class="btn btn-primary" id="submitdtform" name="submitdtform" value="submitdtform">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div
    class="modal fade"
    id="topikEditModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formbedittopik" class="modal-content" method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Edit Pertanyaan</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 center">
                      <h4 class="example-title">Pertanyaan</h4>
                        <textarea class="form-control" id="edit_dtform_pertanyaan" name="dtform_pertanyaan" rows="4"
                        data-fv-notempty="true" data-fv-notempty-message="Wajib Diisi"></textarea>
                    </div>
                    <input type="hidden" id="edit_form_id" name="form_id" value="<?php echo (int) $form_id; ?>"/>
                    <input type="hidden" id="dtform_id" name="dtform_id" />
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <button type="submit" class="btn btn-primary" name="submitdtform" value="submitdtform">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
