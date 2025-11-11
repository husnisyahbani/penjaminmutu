<div class="page">
  <div class="page-content container-fluid">
    <div class="row" data-by-row="true">
      <div class="col-xl-12 col-lg-24S">
        <div class="panel">
          <div class="nav-tabs-horizontal" data-plugin="tabs">
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item" role="presentation"><a class="nav-link active show" data-toggle="tab" href="#exampleTabsOne" aria-controls="exampleTabsOne" role="tab" aria-selected="true">Evaluasi</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsTwo" aria-controls="exampleTabsTwo" role="tab" aria-selected="false">Hasil Evaluasi</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsThree" aria-controls="exampleTabsThree" role="tab" aria-selected="false">Tujuan</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsFour" aria-controls="exampleTabsFour" role="tab" aria-selected="false">Referensi</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsFive" aria-controls="exampleTabsFive" role="tab" aria-selected="false">Hasil</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsSix" aria-controls="exampleTabsSix" role="tab" aria-selected="false">Pertanyaan</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsSeven" aria-controls="exampleTabsSeven" role="tab" aria-selected="false">Temuan</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsEigth" aria-controls="exampleTabsEigth" role="tab" aria-selected="false">Catatan</a></li>
              <li class="dropdown nav-item" role="presentation" style="display: none;">
                <a class="dropdown-toggle nav-link" data-toggle="dropdown" href="#" aria-expanded="false">Menu </a>
                <div class="dropdown-menu" role="menu">
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsOne" aria-controls="exampleTabsOne" role="tab">Evaluasi</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsTwo" aria-controls="exampleTabsTwo" role="tab">Hasil Evaluasi</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsThree" aria-controls="exampleTabsThree" role="tab">Tujuan</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsFour" aria-controls="exampleTabsFour" role="tab">Referensi</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsFive" aria-controls="exampleTabsFive" role="tab">Hasil</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsSix" aria-controls="exampleTabsSix" role="tab">Pertanyaan</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsSeven" aria-controls="exampleTabsSeven" role="tab">Temuan</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsEigth" aria-controls="exampleTabsEigth" role="tab">Catatan</a>
                </div>
              </li>
            </ul>

            <div class="tab-content pt-20">
              <div class="tab-pane active show" id="exampleTabsOne" role="tabpanel">
                <div class="panel">
                  <div class="panel-body">
                    <p><?php if(isset($jawab['dtform_pertanyaan'])) echo $jawab['dtform_pertanyaan'];?></p>
                    <p><?php if(isset($jawab['dtform_lingkup'])) echo $jawab['dtform_lingkup'];?></p>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsTwo" role="tabpanel">
                <div class="panel">
                  <div class="panel-body">
                    <p><?php if(isset($jawab['jwb_jawaban'])) echo $jawab['jwb_jawaban'];?></p>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsThree" role="tabpanel">
                <div class="panel">
                  <header class="panel-heading">
                    <div class="panel-actions panel-actions-keep">
                      <button
                        type="button"
                        class="btn btn-sm btn-icon btn-warning"
                        data-toggle="tooltip"
                        data-original-title="Edit Tujuan"
                        id="edittujuan">
                        <i class="icon md-edit" aria-hidden="true"></i> Edit Tujuan
                      </button>
                    </div>
                  </header>
                  <div class="panel-body">
                    <p><?php if(isset($jawab['jwb_tujuan'])) echo $jawab['jwb_tujuan'];?></p>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsFour" role="tabpanel">
                <div class="panel">
                  <header class="panel-heading">
                    <div class="panel-actions panel-actions-keep">
                      <button type="button" class="btn btn-sm btn-icon btn-warning" data-toggle="tooltip" data-original-title="Edit Referensi" id="editreferensi">
                        <i class="icon md-edit" aria-hidden="true"></i> Edit Referensi
                      </button>
                    </div>
                  </header>
                  <div class="panel-body">
                    <p><?php if(isset($jawab['jwb_referensi'])) echo $jawab['jwb_referensi'];?></p>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsFive" role="tabpanel">
                <div class="panel">
                  <header class="panel-heading">
                    <div class="panel-actions panel-actions-keep">
                      <button type="button" class="btn btn-sm btn-icon btn-warning" data-toggle="tooltip" data-original-title="Edit Hasil" id="edithasil">
                        <i class="icon md-edit" aria-hidden="true"></i> Edit Hasil
                      </button>
                    </div>
                  </header>
                  <div class="panel-body">
                    <p><?php if(isset($jawab['jwb_hasil'])) echo $jawab['jwb_hasil'];?></p>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsSix" role="tabpanel">
                <div class="panel">
                  <header class="panel-heading">
                    <div class="panel-actions panel-actions-keep">
                      <button type="button" class="btn btn-sm btn-icon btn-warning" data-toggle="tooltip" data-original-title="Edit Pertanyaan" id="editpertanyaan">
                        <i class="icon md-edit" aria-hidden="true"></i> Edit Pertanyaan
                      </button>
                    </div>
                  </header>
                  <div class="panel-body">
                    <p><?php if(isset($jawab['jwb_pertanyaan'])) echo $jawab['jwb_pertanyaan'];?></p>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsSeven" role="tabpanel">
                <div class="panel">
                  <header class="panel-heading">
                    <div class="panel-actions panel-actions-keep">
                      <button type="button" class="btn btn-sm btn-icon btn-warning" data-toggle="tooltip" data-original-title="Edit Temuan" id="edittemuan">
                        <i class="icon md-edit" aria-hidden="true"></i> Edit Temuan
                      </button>
                    </div>
                  </header>
                  <div class="panel-body">
                    <p><?php if(isset($jawab['jwb_temuan'])) echo $jawab['jwb_temuan'];?></p>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="exampleTabsEigth" role="tabpanel">
                <div class="panel">
                  <header class="panel-heading">
                    <div class="panel-actions panel-actions-keep">
                      <button type="button" class="btn btn-sm btn-icon btn-warning" data-toggle="tooltip" data-original-title="Edit Catatan" id="editcatatan">
                        <i class="icon md-edit" aria-hidden="true"></i> Edit Catatan
                      </button>
                    </div>
                  </header>
                  <div class="panel-body">
                    <p><?php if(isset($jawab['jwb_catatan'])) echo $jawab['jwb_catatan'];?></p>
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
  id="tujuanModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="exampleFormModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-simple" role="document">
    <div class="modal-content">
      <form id="formtujuan">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
          <h4 class="modal-title" id="exampleFormModalLabel">Tujuan</h4>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 center">
              <h4 class="example-title">Masukkan Tujuan</h4>
              <textarea id="jwb_tujuan" class="editor" name="jwb_tujuan"></textarea>
            </div>
            <input type="hidden" name="audit_id" value="<?=$audit_id?>"/>
            <input type="hidden" name="dtform_id" value="<?=$dtform_id?>"/>
          </div>
        </div>

        <div class="modal-footer text-right">
          <button type="submit" class="btn btn-primary" name="submitjawaban" value="submitjawaban">
            Kirim
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<div
  class="modal fade"
  id="referensiModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="exampleFormModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-simple" role="document">
    <div class="modal-content">
      <form id="formtujuan">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
          <h4 class="modal-title" id="exampleFormModalLabel">Referensi</h4>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 center">
              <h4 class="example-title">Masukkan Referensi</h4>
              <textarea id="jwb_referensi" class="editor" name="jwb_referensi"></textarea>
            </div>
            <input type="hidden" name="audit_id" value="<?=$audit_id?>"/>
            <input type="hidden" name="dtform_id" value="<?=$dtform_id?>"/>
          </div>
        </div>

        <div class="modal-footer text-right">
          <button type="submit" class="btn btn-primary" name="submitjawaban" value="submitjawaban">
            Kirim
          </button>
        </div>

      </form>
    </div>
  </div>
</div>


<div
  class="modal fade"
  id="hasilModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="exampleFormModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-simple" role="document">
    <div class="modal-content">
      <form id="formtujuan">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
          <h4 class="modal-title" id="exampleFormModalLabel">Hasil</h4>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 center">
              <h4 class="example-title">Masukkan Hasil</h4>
              <textarea id="jwb_hasil" class="editor" name="jwb_hasil"></textarea>
            </div>
            <input type="hidden" name="audit_id" value="<?=$audit_id?>"/>
            <input type="hidden" name="dtform_id" value="<?=$dtform_id?>"/>
          </div>
        </div>

        <div class="modal-footer text-right">
          <button type="submit" class="btn btn-primary" name="submitjawaban" value="submitjawaban">
            Kirim
          </button>
        </div>

      </form>
    </div>
  </div>
</div>


<div
  class="modal fade"
  id="pertanyaanModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="exampleFormModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-simple" role="document">
    <div class="modal-content">
      <form id="formtujuan">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
          <h4 class="modal-title" id="exampleFormModalLabel">Pertanyaan</h4>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 center">
              <h4 class="example-title">Masukkan Pertanyaan</h4>
              <textarea id="jwb_pertanyaan" class="editor" name="jwb_pertanyaan"></textarea>
            </div>
            <input type="hidden" name="audit_id" value="<?=$audit_id?>"/>
            <input type="hidden" name="dtform_id" value="<?=$dtform_id?>"/>
          </div>
        </div>

        <div class="modal-footer text-right">
          <button type="submit" class="btn btn-primary" name="submitjawaban" value="submitjawaban">
            Kirim
          </button>
        </div>

      </form>
    </div>
  </div>
</div>


<div
  class="modal fade"
  id="catatanModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="exampleFormModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-simple" role="document">
    <div class="modal-content">
      <form id="formtujuan">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
          <h4 class="modal-title" id="exampleFormModalLabel">Catatan</h4>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 center">
              <h4 class="example-title">Masukkan Catatan</h4>
              <textarea id="jwb_catatan" class="editor" name="jwb_catatan"></textarea>
            </div>
            <input type="hidden" name="audit_id" value="<?=$audit_id?>"/>
            <input type="hidden" name="dtform_id" value="<?=$dtform_id?>"/>
          </div>
        </div>

        <div class="modal-footer text-right">
          <button type="submit" class="btn btn-primary" name="submitjawaban" value="submitjawaban">
            Kirim
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

