<div class="page">
  <div class="page-content container-fluid">

    <?php
    /* Halaman daftar tilik auditor - susunannya mengikuti halaman
       auditee/delik: kartu ringkasan di atas, lalu tabel butir. Tab lama
       (Evaluasi / Hasil Evaluasi / Tujuan / Daftar Tilik) dihapus.

       Di bawah blok tujuan ditampilkan daftar lingkup (mutu_lingkup)
       pertanyaan ini lengkap dengan jawaban auditee dan tautan lampirannya,
       sama seperti tampilan butir lingkup pada halaman auditee. Bagian
       "Jawaban auditee" yang lama (baris tilik) tidak ditampilkan lagi.

       Tujuan pertanyaan disunting langsung di atas tabel (input tujuan),
       dan butir tilik dikelola penuh (CRUD): tambah butir, sunting
       pertanyaan + referensi lewat ikon pada kolom Butir Lingkup, sunting
       Hasil / Temuan / Catatan lewat ikon pada masing-masing kolom, serta
       hapus butir pada kolom aksi.

       Sumber data: mutu_auditjawabdetail (lewat DtjwbModel), sama dengan
       halaman PTK/delik auditee. */
    $ringkas = isset($ringkas) ? $ringkas : array(
        'total' => 0, 'sesuai' => 0, 'observasi' => 0, 'minor' => 0, 'mayor' => 0,
    );
    $jml_dinilai = (int) $ringkas['total'];

    /* Porsi terhadap butir yang sudah dinilai (dipakai bar kartu kategori). */
    $persen = function ($nilai) use ($jml_dinilai) {
        if ($jml_dinilai < 1) {
            return 0;
        }
        return (int) round($nilai * 100 / $jml_dinilai);
    };

    $kartu = array(
        array(
            'judul'   => 'Sesuai',
            'nilai'   => (int) $ringkas['sesuai'],
            'ikon'    => 'md-check-circle',
            'warna'   => '#43a047',
            'ket'     => 'Memenuhi kriteria (S)',
            'bar'     => $persen((int) $ringkas['sesuai']),
            'bar_ket' => $persen((int) $ringkas['sesuai']) . '% dari butir dinilai',
        ),
        array(
            'judul'   => 'Observasi',
            'nilai'   => (int) $ringkas['observasi'],
            'ikon'    => 'md-info-outline',
            'warna'   => '#1e88e5',
            'ket'     => 'Catatan perbaikan (OB)',
            'bar'     => $persen((int) $ringkas['observasi']),
            'bar_ket' => $persen((int) $ringkas['observasi']) . '% dari butir dinilai',
        ),
        array(
            'judul'   => 'Minor',
            'nilai'   => (int) $ringkas['minor'],
            'ikon'    => 'md-alert-triangle',
            'warna'   => '#fb8c00',
            'ket'     => 'Ketidaksesuaian minor (TS MINOR)',
            'bar'     => $persen((int) $ringkas['minor']),
            'bar_ket' => $persen((int) $ringkas['minor']) . '% dari butir dinilai',
        ),
        array(
            'judul'   => 'Mayor',
            'nilai'   => (int) $ringkas['mayor'],
            'ikon'    => 'md-alert-octagon',
            'warna'   => '#e53935',
            'ket'     => 'Ketidaksesuaian mayor (TS MAYOR)',
            'bar'     => $persen((int) $ringkas['mayor']),
            'bar_ket' => $persen((int) $ringkas['mayor']) . '% dari butir dinilai',
        ),
    );
    ?>

    <!-- Informasi pertanyaan + input tujuan -->
    <div class="row" data-by-row="true">
      <div class="col-xl-12">
        <div class="panel">
          <header class="panel-heading panel-heading-filter">
            <div class="panel-heading-isi">
              <h3 class="panel-title"><?php echo html_escape($title); ?></h3>
              <div class="panel-aksi">
                <div class="panel-aksi__tombol">
                  <button type="button" class="btn btn-sm btn-icon btn-warning"
                          audit_id="<?php echo (int) $audit_id; ?>" id="kembali">
                    <i class="icon md-undo" aria-hidden="true"></i>Kembali
                  </button>
                </div>
              </div>
            </div>
          </header>
          <div class="panel-body">
            <div class="topik-ringkas mb-15">
              <?php if (!empty($result['form_nama'])): ?>
              <span class="badge badge-primary"><?php echo html_escape($result['form_nama']); ?></span>
              <?php endif; ?>
              <?php if (isset($soal['dtform_urut']) && (int) $soal['dtform_urut'] > 0): ?>
              <span class="badge badge-info">Pertanyaan ke-<?php echo (int) $soal['dtform_urut']; ?></span>
              <?php endif; ?>
              <span class="badge badge-default" id="jml_butir"><?php echo $jml_dinilai; ?> butir tilik</span>
              <?php if (!empty($result['unit'])): ?>
              <span class="badge badge-default"><?php echo html_escape($result['unit']); ?></span>
              <?php endif; ?>
              <?php if (!empty($result['auditee'])): ?>
              <span class="badge badge-default">Auditee: <?php echo html_escape($result['auditee']); ?></span>
              <?php endif; ?>
            </div>

            <?php if (isset($soal['dtform_pertanyaan']) && trim($soal['dtform_pertanyaan']) !== ''): ?>
            <div class="pertanyaan-isi"><?php echo $soal['dtform_pertanyaan']; ?></div>
            <?php endif; ?>

            <!-- Input tujuan pertanyaan (langsung di atas tabel) -->
            <div class="tujuan-kotak" id="tujuan_kotak">
              <label class="tujuan-label" for="tujuan_isi">
                <i class="icon md-bookmark" aria-hidden="true"></i>Tujuan pertanyaan
              </label>
              <div class="tujuan-tampil" id="tujuan_tampil">
                <span class="tujuan-teks" id="tujuan_teks"><?php
                  echo (isset($jawab['jwb_tujuan']) && trim((string) $jawab['jwb_tujuan']) !== '')
                      ? nl2br(html_escape(lingkup_bersihkan($jawab['jwb_tujuan'])))
                      : '<span class="text-muted">Belum ada tujuan untuk pertanyaan ini.</span>';
                ?></span>
                <button type="button" class="btn btn-xs btn-icon btn-primary" id="tujuan_edit"
                        data-info="Ubah tujuan">
                  <i class="icon md-edit" aria-hidden="true"></i>
                </button>
              </div>
              <div class="tujuan-ubah" id="tujuan_ubah" style="display:none;">
                <textarea class="form-control" id="tujuan_isi" rows="3"
                          placeholder="Tulis tujuan pertanyaan ini..."><?php
                  echo html_escape(lingkup_bersihkan(isset($jawab['jwb_tujuan']) ? $jawab['jwb_tujuan'] : ''));
                ?></textarea>
                <div class="tujuan-aksi">
                  <button type="button" class="btn btn-sm btn-primary" id="tujuan_simpan"
                          audit_id="<?php echo (int) $audit_id; ?>"
                          dtform_id="<?php echo (int) $dtform_id; ?>">
                    <i class="icon md-check" aria-hidden="true"></i>Simpan Tujuan
                  </button>
                  <button type="button" class="btn btn-sm btn-default" id="tujuan_batal">Batal</button>
                  <span class="tujuan-pesan text-muted"></span>
                </div>
              </div>
            </div><!-- /#tujuan_kotak - tujuan tetap satu blok tersendiri -->

            <?php
            /* Daftar lingkup (mutu_lingkup) pertanyaan ini lengkap dengan
               jawaban auditee dan lampirannya - tampilannya mengikuti butir
               lingkup pada halaman auditee/dashboard/detail. Bagian
               "Jawaban auditee" yang lama (baris tilik) sudah dihapus dari
               halaman ini. Penilaian auditor (hasil/temuan/catatan) juga
               tidak ditampilkan di sini karena diisi pada tabel Daftar
               Tilik di bawah. */
            $lingkup_jawab = isset($lingkup_jawab) ? $lingkup_jawab : array();
            ?>
            <?php if (!empty($lingkup_jawab)): ?>
            <div class="pertanyaan-butir">
              <?php /* Tombol buka/tutup: daftar lingkup dapat disembunyikan
                       atau ditampilkan lagi (collapse Bootstrap). */ ?>
              <button type="button" class="btn btn-sm btn-default lingkup-toggle" data-toggle="collapse"
                      data-target="#butir_lingkup" aria-expanded="false" aria-controls="butir_lingkup">
                <i class="icon md-chevron-down lingkup-toggle__ikon" aria-hidden="true"></i>
                Daftar lingkup &amp; jawaban auditee
                <span class="badge badge-default"><?php echo count($lingkup_jawab); ?> butir</span>
              </button>
              <div class="collapse pertanyaan-butir__daftar" id="butir_lingkup">
                <?php $nomor = 0; ?>
                <?php foreach ($lingkup_jawab as $b):
                    $nomor++;
                    $jawaban = lingkup_bersihkan(isset($b['jwb_jawaban']) ? $b['jwb_jawaban'] : '');
                    $sudah   = trim($jawaban) !== '';
                ?>
                <div class="aktivitas lingkup-item" data-lingkup_id="<?php echo (int) $b['lingkup_id']; ?>">
                  <div class="aktivitas-isi">
                    <span class="aktivitas-teks">
                      <span class="lingkup-nomor"><?php echo $nomor; ?>.</span>
                      <?php echo html_escape($b['lingkup_teks']); ?>
                      <span class="badge lingkup-status <?php echo $sudah ? 'badge-success' : 'badge-warning'; ?>">
                        <?php echo $sudah ? 'sudah dijawab' : 'belum dijawab'; ?>
                      </span>
                    </span>
                    <div class="jawaban-kotak">
                      <div class="jawaban-teks">
                        <?php echo $sudah
                            ? nl2br(html_escape($jawaban))
                            : '<span class="text-muted">belum dijawab</span>'; ?>
                      </div>
                    </div>
                    <?php if (!empty($b['lampiran'])): ?>
                    <div class="lampiran-kotak">
                      <span class="lampiran-judul">
                        <i class="icon md-attachment" aria-hidden="true"></i>Lampiran
                      </span>
                      <div class="lampiran-daftar">
                        <?php foreach ($b['lampiran'] as $l): ?>
                        <div class="lampiran" data-lampiran_id="<?php echo (int) $l['lampiran_id']; ?>">
                          <i class="icon md-file lampiran-ikon" aria-hidden="true"></i>
                          <a class="lampiran-nama" target="_blank" rel="noopener"
                             href="<?php echo $l['url']; ?>"><?php echo html_escape($l['lampiran_asli']); ?></a>
                          <span class="lampiran-ukuran text-muted"><?php echo html_escape($l['ukuran_teks']); ?></span>
                        </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Kartu ringkasan penilaian pertanyaan ini -->
    <div class="row" data-plugin="matchHeight" data-by-row="true">
      <?php foreach ($kartu as $k): ?>
      <div class="col-xl-3 col-md-6">
        <div class="kartu-stat" style="background-color:<?php echo $k['warna']; ?>;">
          <i class="icon <?php echo $k['ikon']; ?> kartu-stat__ikon" aria-hidden="true"></i>
          <div class="kartu-stat__isi">
            <div class="kartu-stat__angka"><?php echo number_format($k['nilai'], 0, ',', '.'); ?></div>
            <div class="kartu-stat__judul"><?php echo $k['judul']; ?></div>
            <div class="kartu-stat__ket"><?php echo $k['ket']; ?></div>
            <?php if ($k['bar_ket'] !== ''): ?>
            <div class="kartu-stat__bar" role="img" aria-label="<?php echo html_escape($k['bar_ket']); ?>">
              <span class="kartu-stat__bar-isi" style="width:<?php echo (int) $k['bar']; ?>%;"></span>
            </div>
            <div class="kartu-stat__bar-ket"><?php echo $k['bar_ket']; ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Daftar butir tilik pertanyaan ini -->
    <div class="row" data-by-row="true">
      <div class="col-xl-12">
        <div class="panel">
          <header class="panel-heading panel-heading-filter">
            <div class="panel-heading-isi">
              <h3 class="panel-title">Daftar Tilik</h3>
              <div class="panel-aksi">
                <div class="panel-aksi__tombol">
                  <button type="button" class="btn btn-sm btn-primary" id="tambahtilik">
                    <i class="icon md-plus" aria-hidden="true"></i>Tambah Butir Tilik
                  </button>
                </div>
              </div>
            </div>
          </header>
          <div class="panel-body">
            <div class="ptk-tabel-kotak">
              <table class="table table-hover ptk-tabel" id="tilik">
                <thead>
                  <tr>
                    <th class="ptk-kolom-no">No</th>
                    <th class="ptk-kolom-butir">Butir Lingkup</th>
                    <th class="ptk-kolom-hasil">Hasil</th>
                    <th class="ptk-kolom-temuan">Temuan</th>
                    <th class="ptk-kolom-catatan">Catatan</th>
                    <th class="ptk-kolom-aksi">Aksi</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal Tambah Butir Tilik -->
<div class="modal fade" id="tambahTilikModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-simple modal-lg" role="document">
    <div class="modal-content">
      <form id="formtilik">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Tambah Butir Tilik</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 form-group">
              <label class="form-control-label" for="dtjwb_pertanyaan">Pertanyaan / Butir Tilik</label>
              <textarea class="form-control" id="dtjwb_pertanyaan" name="dtjwb_pertanyaan" rows="3"
                        placeholder="Tulis pertanyaan / butir tilik"
                        data-fv-notempty="true" data-fv-notempty-message="Wajib Diisi"></textarea>
            </div>
            <div class="col-md-12 form-group">
              <label class="form-control-label" for="dtjwb_referensi">Referensi</label>
              <input type="text" class="form-control" id="dtjwb_referensi" name="dtjwb_referensi"
                     placeholder="Referensi (opsional)"/>
            </div>
          </div>
        </div>
        <div class="modal-footer text-right">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Ubah Butir Tilik (pertanyaan + referensi) -->
<div class="modal fade" id="editButirModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-simple modal-lg" role="document">
    <div class="modal-content">
      <form id="formbutir">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Ubah Butir Tilik</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 form-group">
              <label class="form-control-label" for="edit_dtjwb_pertanyaan">Pertanyaan / Butir Tilik</label>
              <textarea class="form-control" id="edit_dtjwb_pertanyaan" name="edit_dtjwb_pertanyaan" rows="3"
                        placeholder="Tulis pertanyaan / butir tilik"
                        data-fv-notempty="true" data-fv-notempty-message="Wajib Diisi"></textarea>
            </div>
            <div class="col-md-12 form-group">
              <label class="form-control-label" for="edit_dtjwb_referensi">Referensi</label>
              <input type="text" class="form-control" id="edit_dtjwb_referensi" name="edit_dtjwb_referensi"
                     placeholder="Referensi (opsional)"/>
            </div>
            <input type="hidden" id="pertanyaan_dtjwb_id" name="pertanyaan_dtjwb_id"/>
          </div>
        </div>
        <div class="modal-footer text-right">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Ubah Isian (hasil / temuan / catatan) -->
<div class="modal fade" id="editIsiModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-simple modal-lg" role="document">
    <div class="modal-content">
      <form id="formisi">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title" id="editIsiJudul">Ubah Isian</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 form-group" id="editIsiTeks">
              <label class="form-control-label" for="edit_isi_nilai">Isian</label>
              <textarea class="form-control" id="edit_isi_nilai" rows="3"
                        placeholder="Tulis isian"></textarea>
            </div>
            <div class="col-md-12 form-group" id="editIsiTemuan" style="display:none;">
              <label class="form-control-label" for="edit_isi_temuan">Temuan</label>
              <select class="form-control" id="edit_isi_temuan">
                <option value="">--Pilih Temuan--</option>
                <option value="S">S (Sesuai)</option>
                <option value="OB">OB (Observasi)</option>
                <option value="TS MINOR">TS MINOR</option>
                <option value="TS MAYOR">TS MAYOR</option>
              </select>
            </div>
            <input type="hidden" id="edit_isi_dtjwb_id"/>
            <input type="hidden" id="edit_isi_kolom"/>
          </div>
        </div>
        <div class="modal-footer text-right">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
