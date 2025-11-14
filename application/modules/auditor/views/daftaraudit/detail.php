<div class="page">
    <div class="page-content container-fluid">
        
        
        <div class="row"  data-by-row="true">
            
            <div class="col-xl-12 col-md-24">

                <div class="panel">
                    <header class="panel-heading">
                        <h3 class="panel-title"><?=$result['form_nama']?></h3>
                        <div class="panel-actions panel-actions-keep">
                           
                        </div>
                    </header>
                    <div class="panel-body">
                        <table class="table table-hover dataTable w-full" id="daftarpertanyaan">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Evaluasi</th>
                                    <th>Delik dan Jawaban</th>
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

<div
  class="modal fade"
  id="tujuanModal"
  aria-hidden="false"
  aria-labelledby="exampleFormModalLabel"
  role="dialog"
  tabindex="-1">
  
  <div class="modal-dialog modal-simple">
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
          </div>
        </div>

        <div class="modal-footer text-right">
          <button type="submit" class="btn btn-primary" id="submitjawaban" name="submitjawaban" value="submitjawaban">
            Kirim
          </button>
        </div>

      </form>
    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->


<div
    class="modal fade"
    id="referensiModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formreferensi" class="modal-content">
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

<div
    class="modal fade"
    id="pertanyaanModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formpertanyaan" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="exampleFormModalLabel">Lingkup Pertanyaan</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                        <h4 class="example-title">Masukkan Pertanyaan Anda</h4>
                        <textarea id="jwb_pertanyaan" class="editor" name="jwb_pertanyaan"></textarea>
                    </div>
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

<div
    class="modal fade"
    id="hasilModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formhasil" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="exampleFormModalLabel">Masukkan Hasil</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                        <h4 class="example-title">Hasil</h4>
                        <textarea id="jwb_hasil" class="editor" name="jwb_hasil"></textarea>
                    </div>
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

<div
    class="modal fade"
    id="temuanModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formtemuan" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="exampleFormModalLabel">TEMUAN</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                        <h4 class="example-title">Pilih Temuan</h4>
                        <select class="form-control" name="jwb_temuan" id="jwb_temuan" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Dipilih">                                 
                            <option value="S">S</option>
                            <option value="OB">OB</option>
                            <option value="TS MINOR">TS MINOR</option>
                            <option value="TS MAYOR">TS MAYOR</option>
                        </select>
                    </div>
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


<div
    class="modal fade"
    id="catatanModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formcatatan" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="exampleFormModalLabel">CATATAN</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                        <h4 class="example-title">Masukkan Catatan Anda</h4>
                        <textarea id="jwb_catatan" class="editor" name="jwb_catatan" readonly></textarea>
                        
                    </div>
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