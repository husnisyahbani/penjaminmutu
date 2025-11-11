<!-- =========================== -->
<!-- Halaman Utama -->
<!-- =========================== -->
<div class="page">
  <div class="page-content container-fluid">
    <div class="row" data-by-row="true">
      <div class="col-xl-12 col-lg-24S">
        <div class="panel">

          <!-- Tabs -->
          <div class="nav-tabs-horizontal" data-plugin="tabs">
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item" role="presentation"><a class="nav-link active show" data-toggle="tab" href="#exampleTabsOne" role="tab">Evaluasi</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsTwo" role="tab">Hasil Evaluasi</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsThree" role="tab">Tujuan</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsFour" role="tab">Referensi</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsFive" role="tab">Hasil</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsSix" role="tab">Pertanyaan</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsSeven" role="tab">Temuan</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsEigth" role="tab">Catatan</a></li>
            </ul>

            <div class="tab-content pt-20">
              
              <!-- TAB TUJUAN -->
              <div class="tab-pane" id="exampleTabsThree" role="tabpanel">
                <div class="panel">
                  <header class="panel-heading">
                    <div class="panel-actions panel-actions-keep">
                      <button type="button" class="btn btn-sm btn-icon btn-warning" id="edittujuan">
                        <i class="icon md-edit" aria-hidden="true"></i> Edit Tujuan
                      </button>
                    </div>
                  </header>
                  <div class="panel-body">
                    <p><?php if(isset($jawab['jwb_tujuan'])) echo $jawab['jwb_tujuan'];?></p>
                  </div>
                </div>
              </div>

              <!-- TAB LAIN -->
              <div class="tab-pane active show" id="exampleTabsOne" role="tabpanel">
                <div class="panel">
                  <div class="panel-body">
                    <p><?php if(isset($jawab['dtform_pertanyaan'])) echo $jawab['dtform_pertanyaan'];?></p>
                    <p><?php if(isset($jawab['dtform_lingkup'])) echo $jawab['dtform_lingkup'];?></p>
                  </div>
                </div>
              </div>

              <!-- dst... tab lain tidak saya ubah -->
              
            </div> <!-- end .tab-content -->
          </div> <!-- end .nav-tabs-horizontal -->
        </div>
      </div>
    </div>
  </div>
</div>


<!-- =========================== -->
<!-- MODAL TUJUAN -->
<!-- =========================== -->
<div class="modal fade" id="tujuanModal" tabindex="-1" role="dialog" aria-labelledby="exampleFormModalLabel" aria-hidden="true">
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
              <textarea id="jwb_tujuan" class="editor form-control" name="jwb_tujuan" rows="5"></textarea>
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


<!-- =========================== -->
<!-- JAVASCRIPT -->
<!-- =========================== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {

  // Ketika tombol Edit Tujuan diklik
  $('#edittujuan').on('click', function() {
    $('#tujuanModal').modal('show');
  });

  // (Opsional) Saat submit form tujuan
  $('#formtujuan').on('submit', function(e) {
    e.preventDefault();

    // contoh ajax kirim data
    $.post('<?=base_url("controller/simpan_tujuan")?>', $(this).serialize(), function(res) {
      alert('Tujuan berhasil disimpan!');
      $('#tujuanModal').modal('hide');
    });
  });
});
</script>
