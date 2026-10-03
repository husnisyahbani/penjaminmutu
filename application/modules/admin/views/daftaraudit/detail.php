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
                                    <th width="20%">Hasil Evaluasi dan Delik</th>
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
