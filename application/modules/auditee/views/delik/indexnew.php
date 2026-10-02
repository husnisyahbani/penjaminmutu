<div class="page">
  <div class="page-content container-fluid">
    <div class="row" data-by-row="true">
      <div class="col-xl-12 col-lg-24S">
        <div class="panel">
          <div class="panel-heading">
                        <h3 class="panel-title"><?=$title;?></h3>
                        <div class="panel-actions panel-actions-keep">
                            <button type="button" class="btn btn-sm btn-icon btn-warning" audit_id="<?=$audit_id?>" id="kembali">
                                <i class="icon md-undo" aria-hidden="true"></i>Kembali
                            </button>
                        </div>
                    </div>
          <div class="nav-tabs-horizontal" data-plugin="tabs">
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item" role="presentation"><a class="nav-link active show" data-toggle="tab" href="#exampleTabsOne" aria-controls="exampleTabsOne" role="tab" aria-selected="true">Evaluasi</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsTwo" aria-controls="exampleTabsTwo" role="tab" aria-selected="false">Hasil Evaluasi</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsThree" aria-controls="exampleTabsThree" role="tab" aria-selected="false">Tujuan</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsFour" aria-controls="exampleTabsFour" role="tab" aria-selected="false">Referensi</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsFive" aria-controls="exampleTabsFive" role="tab" aria-selected="false">Pertanyaan</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsSix" aria-controls="exampleTabsSix" role="tab" aria-selected="false">Hasil</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsSeven" aria-controls="exampleTabsSeven" role="tab" aria-selected="false">Temuan</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsEigth" aria-controls="exampleTabsEigth" role="tab" aria-selected="false">Catatan</a></li>
              <li class="dropdown nav-item" role="presentation" style="display: none;">
                <a class="dropdown-toggle nav-link" data-toggle="dropdown" href="#" aria-expanded="false">Menu </a>
                <div class="dropdown-menu" role="menu">
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsOne" aria-controls="exampleTabsOne" role="tab">Evaluasi</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsTwo" aria-controls="exampleTabsTwo" role="tab">Hasil Evaluasi</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsThree" aria-controls="exampleTabsThree" role="tab">Tujuan</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsFour" aria-controls="exampleTabsFour" role="tab">Referensi</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsFive" aria-controls="exampleTabsFive" role="tab">Pertanyaan</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsSix" aria-controls="exampleTabsSix" role="tab">Hasil</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsSeven" aria-controls="exampleTabsSeven" role="tab">Temuan</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsEigth" aria-controls="exampleTabsEigth" role="tab">Catatan</a>
                </div>
              </li>
            </ul>

            <div class="tab-content pt-20">
              <div class="tab-pane active show" id="exampleTabsOne" role="tabpanel">
                <div class="panel">
                  <div class="panel-body">
                    <p><?php if(isset($soal['dtform_pertanyaan'])) echo $soal['dtform_pertanyaan'];?></p>
                    <?php if(!empty($lingkup)): ?>
                    <p class="font-weight-600 mb-5">Butir lingkup yang dinilai:</p>
                    <div id="pertanyaan"><?php echo $lingkup; ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsTwo" role="tabpanel">
                <div class="panel">
                  <header class="panel-heading">
                    <div class="panel-actions panel-actions-keep">
                      <?php if($result['audit_status'] === 'DRAFT'){?>
                      <button
                        type="button"
                        class="btn btn-sm btn-icon btn-warning"
                        data-toggle="tooltip"
                        data-original-title="Masukkan Jawaban"
                        id="editjawaban">
                        <i class="icon md-edit" aria-hidden="true"></i> Masukkan Jawaban
                      </button>
                      <?php } ?>
                    </div>
                  </header>
                  <div class="panel-body">
                    <p id="jawaban"><?php if(isset($jawab['jwb_jawaban'])){ $allowed_tags = '<p><br><b><i><u><strong><em><ul><ol><li>';
$jawab['jwb_jawaban'] = strip_tags($jawab['jwb_jawaban'], $allowed_tags);
echo $jawab['jwb_jawaban'];}?></p>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsThree" role="tabpanel">
                <div class="panel">
                  
                  <div class="panel-body">
                    <div id="tujuan"><p></p><?php if(isset($jawab['jwb_tujuan'])) echo $jawab['jwb_tujuan'];?></div>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsFour" role="tabpanel">
                <div class="panel">
                  <header class="panel-heading">
                    <div class="panel-actions panel-actions-keep">
                      
                    </div>
                  </header>
                  <div class="panel-body">
                    <div id="referensi"><p></p><?php if(isset($jawab['jwb_referensi'])){ $allowed_tags = '<p><br><b><i><u><strong><em><ul><ol><li>';
$jawab['jwb_referensi'] = strip_tags($jawab['jwb_referensi'], $allowed_tags);
echo $jawab['jwb_referensi'];}?></div>
                  </div>
                </div>
              </div>

              

              <div class="tab-pane" id="exampleTabsFive" role="tabpanel">
                <div class="panel">
                  <header class="panel-heading">
                    <div class="panel-actions panel-actions-keep">
                      
                    </div>
                  </header>
                  <div class="panel-body">
                    <div id="pertanyaan"><p></p><?php if(isset($jawab['jwb_pertanyaan'])) echo $jawab['jwb_pertanyaan'];?></div>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsSix" role="tabpanel">
                <div class="panel">
                  <header class="panel-heading">
                    <div class="panel-actions panel-actions-keep">
                      
                    </div>
                  </header>
                  <div class="panel-body">
                    <div id="hasil"><p></p><?php if(isset($jawab['jwb_hasil'])) echo $jawab['jwb_hasil'];?></div>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsSeven" role="tabpanel">
                <div class="panel">
                  <div class="panel-body">
                    <?php
                    /* Butir yang sudah dinilai pada pertanyaan ini - tabel
                       yang sama seperti halaman PTK, TANPA filter jenis
                       penilaian: butir "S" (sesuai) ikut tampil, hanya butir
                       yang belum dinilai auditor yang tidak ikut. Data dari
                       controller: $butir (AuditjawabModel::butirDinilai). */
                    ?>
                    <?php if (empty($butir)): ?>
                    <div class="topik-kosong">Belum ada butir yang dinilai pada pertanyaan ini.</div>
                    <?php else: ?>
                    <div class="ptk-tabel-kotak">
                      <table class="table table-hover ptk-tabel butir-tabel">
                        <thead>
                          <tr>
                            <th class="butir-kolom-no">No</th>
                            <th class="butir-kolom-isi">Butir Lingkup</th>
                            <th class="butir-kolom-hasil">Hasil</th>
                            <th class="butir-kolom-temuan">Temuan</th>
                            <th class="butir-kolom-catatan">Catatan</th>
                            <th class="butir-kolom-koreksi">Rencana Koreksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($butir as $i => $b):
                              $isi     = lingkup_bersihkan($b['dtjwb_pertanyaan']);
                              $hasil   = lingkup_bersihkan($b['dtjwb_hasil']);
                              $temuan  = trim((string) $b['dtjwb_temuan']);
                              $catatan = lingkup_bersihkan($b['dtjwb_catatan']);
                              $koreksi = lingkup_teks_baris($b['dtjwb_koreksi']);
                          ?>
                          <tr>
                            <td class="butir-kolom-no"><?php echo $i + 1; ?></td>
                            <td>
                              <div class="butir-klamp"><?php echo html_escape($isi); ?></div>
                            </td>
                            <td><div class="butir-klamp"><?php echo html_escape($hasil); ?></div></td>
                            <td class="text-center">
                              <span class="badge badge-warning"><?php echo html_escape($temuan); ?></span>
                            </td>
                            <td><div class="butir-klamp"><?php echo html_escape($catatan); ?></div></td>
                            <td>
                              <?php if ($koreksi === ''): ?>
                              <span class="butir-koreksi-kosong">Belum ada rencana koreksi</span>
                              <?php else: ?>
                              <div class="butir-klamp"><?php echo nl2br(html_escape($koreksi)); ?></div>
                              <?php endif; ?>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsEigth" role="tabpanel">
                <div class="panel">
                  <header class="panel-heading">
                    <div class="panel-actions panel-actions-keep">
                      
                    </div>
                  </header>
                  <div class="panel-body">
                    <div id="catatan"><p></p><?php if(isset($jawab['jwb_catatan'])) echo $jawab['jwb_catatan'];?></div>
                  </div>
                </div>
              </div>

            </div><!-- /.tab-content -->
          </div><!-- /.nav-tabs-horizontal -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tujuan (TIDAK diubah isinya, hanya atribut standar Bootstrap) -->
<div
    class="modal fade"
    id="editModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formjawaban" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="exampleFormModalLabel">Jawaban</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                        <h4 class="example-title">Pertanyaan</h4>
                        <p><?php if(isset($soal['dtform_pertanyaan'])) echo $soal['dtform_pertanyaan'];?></p>
                        <?php if(!empty($lingkup)) echo $lingkup; ?>
                    </div>
                    <div class="col-md-12 center">
                        <h4 class="example-title">Jawaban</h4>
                        <textarea id="jwb_jawaban" class="editor" name="jwb_jawaban"></textarea>
                    </div>

                    <input type="hidden" name="audit_id" value="<?=$audit_id?>"/>
                    <input type="hidden" name="dtform_id" value="<?=$dtform_id?>"/>
                </div>

                

            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" id="submitjawaban" name="submitjawaban" value="submitjawaban">Kirim</button>
                </div>
            </div>

        </div>
    </form>
</div>


