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
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsFour" aria-controls="exampleTabsFour" role="tab" aria-selected="false">Daftar Tilik</a></li>
              <li class="dropdown nav-item" role="presentation" style="display: none;">
                <a class="dropdown-toggle nav-link" data-toggle="dropdown" href="#" aria-expanded="false">Menu </a>
                <div class="dropdown-menu" role="menu">
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsOne" aria-controls="exampleTabsOne" role="tab">Evaluasi</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsTwo" aria-controls="exampleTabsTwo" role="tab">Hasil Evaluasi</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsThree" aria-controls="exampleTabsThree" role="tab">Tujuan</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsFour" aria-controls="exampleTabsFour" role="tab">Daftar Tilik</a>
                  <!-- <a class="dropdown-item" data-toggle="tab" href="#exampleTabsFive" aria-controls="exampleTabsFive" role="tab">Pertanyaan</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsSix" aria-controls="exampleTabsSix" role="tab">Hasil</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsSeven" aria-controls="exampleTabsSeven" role="tab">Temuan</a>
                  <a class="dropdown-item" data-toggle="tab" href="#exampleTabsEigth" aria-controls="exampleTabsEigth" role="tab">Catatan</a> -->
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
                    <p><?php if(isset($jawab['jwb_jawaban'])){ $allowed_tags = '<p><br><b><i><u><strong><em><ul><ol><li>';
$jawab['jwb_jawaban'] = strip_tags($jawab['jwb_jawaban'], $allowed_tags);
echo $jawab['jwb_jawaban'];}?></p>
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
                  <br/>
                  <div class="panel-body">
                        <table class="table table-hover dataTable table-striped w-full" id="tilik">
                        <thead>
                            <tr>
                                <th width="5px">No</th>
                                <th>Pertanyaan</th>
                                <th>Hasil</th>
                                <th>Temuan</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                        </tbody>
                        </table>
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
  <div class="modal-dialog modal-simple modal-lg" role="document">
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
          <button type="submit" class="btn btn-primary" id="submitjawaban" name="submitjawaban" value="submitjawaban">
            Kirim
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<div
  class="modal fade"
  id="tilikModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="exampleFormModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-simple modal-lg" role="document">
    <div class="modal-content">
      <form id="formtilik">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
          <h4 class="modal-title" id="exampleFormModalLabel">Tambah Tilik</h4>
        </div>

        <div class="modal-body">
          
          <div class="row">
            <div class="col-md-12 form-group center">
              <h4 class="example-title">Referensi</h4>
                        <input type="text" class="form-control" id="dtjwb_referensi" name="dtjwb_referensi" placeholder="Masukkan Referensi" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>

            <div class="col-md-12 form-group center">
              <h4 class="example-title">Pertanyaan</h4>
              <input type="text" class="form-control" id="dtjwb_pertanyaan" name="dtjwb_pertanyaan" placeholder="Masukkan Pertanyaan" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
            </div>
            
            <input type="hidden" name="jwb_id" value="<?=$jwb_id?>"/>
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
    id="editPertanyaanModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple modal-lg">
        <form id="formpertanyaan" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="informasiLabel">Edit Pertanyaan</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                  <div class="col-md-12 form-group center">
                      <h4 class="example-title">Referensi</h4>
                        <input type="text" class="form-control" id="edit_dtjwb_referensi" name="edit_dtjwb_referensi" placeholder="Masukkan Referensi" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    <div class="col-md-12 form-group center">
                        <h4 class="example-title">Pertanyaan</h4>
                        <input type="text" class="form-control" id="edit_dtjwb_pertanyaan" name="edit_dtjwb_pertanyaan" placeholder="Masukkan Pertanyaan" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    
                    
                    <input type="hidden" id="pertanyaan_dtjwb_id" name="pertanyaan_dtjwb_id" />
                </div>
            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" id="submitpertanyaan" name="submitpertanyaan" value="submitpertanyaan">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div
    class="modal fade"
    id="editHasilModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple modal-lg">
        <form id="formhasil" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="informasiLabel">Edit Hasil</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                        <input type="text" class="form-control" id="edit_dtjwb_hasil" name="edit_dtjwb_hasil" placeholder="Masukkan Hasil" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    
                    <input type="hidden" id="hasil_dtjwb_id" name="hasil_dtjwb_id" />
                </div>
            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" id="submithasil" name="submithasil" value="submithasil">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>


<div
  class="modal fade"
  id="editTemuanModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="exampleFormModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-simple modal-lg" role="document">
    <div class="modal-content">
      <form id="formtemuan">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
          <h4 class="modal-title" id="exampleFormModalLabel">Temuan</h4>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 center">
              <h4 class="example-title">Masukkan Temuan</h4>
              <select class="form-control" name="edit_dtjwb_temuan" id="edit_dtjwb_temuan" data-fv-notempty="true"
                    data-fv-notempty-message="Wajib Dipilih"> 
                                  <option value="">--Pilih Temuan--</option>                                
                                  <option value="S">S</option>
                                  <option value="OB">OB</option>
                                  <option value="TS MINOR">TS MINOR</option>
                                  <option value="TS MAYOR">TS MAYOR</option>
                                </select>
            </div>
            <input type="hidden" id="temuan_dtjwb_id" name="temuan_dtjwb_id" />
          </div>
        </div>

        <div class="modal-footer text-right">
          <button type="submit" class="btn btn-primary" name="submittemuan" value="submittemuan">
            Kirim
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<div
    class="modal fade"
    id="editCatatanModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple modal-lg">
        <form id="formcatatan" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="informasiLabel">Edit Catatan</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                        <input type="text" class="form-control" id="edit_dtjwb_catatan" name="edit_dtjwb_catatan" placeholder="Masukkan Catatan" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Diisi"/>
                    </div>
                    
                    <input type="hidden" id="catatan_dtjwb_id" name="catatan_dtjwb_id" />
                </div>
            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" id="submitcatatan" name="submitcatatan" value="submitcatatan">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>

