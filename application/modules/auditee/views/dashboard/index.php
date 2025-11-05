<div class="page">
    <div class="page-content container-fluid">
        
        <div class="row" data-plugin="matchHeight" data-by-row="true">

            <div class="col-xl-3 col-md-6">
                <div class="card card-block p-25 bg-blue-600">
                    <div class="counter counter-lg counter-inverse">
                        <div class="counter-label text-uppercase">AUDIT DRAFT</div>
                        <div class="counter-number-group">
                            <span class="counter-number-related"></span>
                            <span class="counter-number">
                            <?php if(isset($totaldraft)){echo number_format($totaldraft,0,',','.');}else{echo '0';}?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-block p-25 bg-red-600">
                    <div class="counter counter-lg counter-inverse">
                        <div class="counter-label text-uppercase">AUDIT TERKIRIM</div>
                        <div class="counter-number-group">
                            <span class="counter-number-related"></span>
                            <span class="counter-number">
                            <?php if(isset($totalterkirim)){echo number_format($totalterkirim,0,',','.');}else{echo '0';}?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-block p-25 bg-orange-600">
                    <div class="counter counter-lg counter-inverse">
                        <div class="counter-label text-uppercase">AUDIT PROSES</div>
                        <div class="counter-number-group">
                            <span class="counter-number-related"></span>
                            <span class="counter-number">
                            <?php if(isset($totalproses)){echo number_format($totalproses,0,',','.');}else{echo '0';}?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-block p-25 bg-green-600">
                    <div class="counter counter-lg counter-inverse">
                        <div class="counter-label text-uppercase">AUDIT SELESAI</div>
                        <div class="counter-number-group">
                            <span class="counter-number-related"></span>
                            <span class="counter-number">
                            <?php if(isset($totalselesai)){echo number_format($totalselesai,0,',','.');}else{echo '0';}?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            

        </div>
        <div class="row"  data-by-row="true">
            
            <div class="col-xl-12 col-md-24">

                <div class="panel">
                    <header class="panel-heading">
                        <h3 class="panel-title">AUDIT</h3>
                        <div class="panel-actions panel-actions-keep">
                            
                            <!-- <button type="button" class="btn btn-sm btn-icon btn-success" id="tambah">
                                <i class="icon md-plus" aria-hidden="true"></i>Tambah
                            </button> -->
                        </div>
                    </header>
                    <div class="panel-body">
                        <table class="table table-hover dataTable w-full" id="daftaraudit">
                            <thead>
                                <tr>
                                    <th >No</th>
                                    <th >Formulir</th>
                                    <th >Auditor</th>
                                    <th >Auditee</th>
                                    <th >Unit</th>
                                    <th> Aksi</th>
                                    <th> Status</th>
                                </tr>
                            </thead>

                            <tbody>
                            

                            </tbody>
                        </table>
                    </div>
                </div>

                

            </div>


            <!-- <div class="col-xl-12 col-md-24">
                <div class="panel">
                    <header class="panel-heading">
                        <h3 class="panel-title">Detail Pertanyaan</h3>
                        <div class="panel-actions panel-actions-keep">
                            
                        </div>
                    </header>
                    <div class="panel-body">
                        <table class="table table-hover dataTable w-full" id="daftarpertanyaan">
                            <thead>
                                <tr>
                                    <th >No</th>
                                    <th >Pertanyaan</th>
                                    <th >Ruang Lingkup</th>
                                    <th >Jawaban</th>
                                    <th >Hasil</th>
                                    <th >Temuan</th>
                                    <th >Catatan</th>
                                </tr>
                            </thead>

                            <tbody>
                                

                            </tbody>
                        </table>
                    </div>
                </div>
            </div> -->
            
        </div>
    </div>
</div>

<div
    class="modal fade"
    id="addModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formadd" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="exampleFormModalLabel">AUDIT</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                        <select class="form-control" name="form_id" id="form_id" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Dipilih">                                 
                            <option value="">-- Pilih Formulir Audit --</option>
                            <?php
                                foreach($formulir as $row){
                                    echo '<option value="'.$row['form_id'].'">'.$row['form_nama'].'</option>';
                                } 
                            ?>
                        </select>
                    </div>

                    <div class="col-md-12 center">
                        <h4 class="example-title">Auditor</h4>
                        <select class="form-control" name="auditor" id="auditor" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Dipilih">                                 
                            <option value="">-- Pilih Auditor --</option>
                            <?php
                                foreach($listauditor as $row){
                                    echo '<option value="'.$row['users_id'].'">'.$row['nama'].'</option>';
                                } 
                            ?>
                        </select>
                    </div>

                    <div class="col-md-12 center">
                        <h4 class="example-title">Auditee</h4>
                        <select class="form-control" name="auditee" id="auditee" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Dipilih">                                 
                            <option value="">-- Pilih Auditee --</option>
                            <?php
                                foreach($listauditee as $row){
                                    echo '<option value="'.$row['users_id'].'">'.$row['nama'].'</option>';
                                } 
                            ?>
                        </select>
                    </div>
                </div>

                

            </div>

            <div class="modal-footer">
            <div class="text-right">
                    <button type="submit" class="btn btn-primary" id="submitajuan" name="submitajuan" value="submitajuan">Simpan</button>
                </div>
            </div>

        </div>
    </form>
</div>


<div
    class="modal fade"
    id="editModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formedit" class="modal-content">
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
                        <textarea id="pertanyaan" class="editor" name="pertanyaan" readonly></textarea>
                    </div>
                    <div class="col-md-12 center">
                        <h4 class="example-title">Jawaban</h4>
                        <textarea id="jwb_jawaban" class="editor" name="jwb_jawaban"></textarea>
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

<div class="modal fade example-modal-lg" aria-hidden="true" aria-labelledby="exampleOptionalLarge"
                      id="pertanyaanModal" role="dialog" tabindex="-1" data-bs-backdrop="false">
                      <div class="modal-dialog modal-simple modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" id="exampleOptionalLarge">Detail Pertanyaan</h4>
                          </div>
                          <div class="modal-body">
                            <table class="table table-hover dataTable w-full" id="daftarpertanyaan">
                            <thead>
                                <tr>
                                    <th >No</th>
                                    <th >Pertanyaan</th>
                                    <th >Jawaban</th>
                                    <th >Hasil</th>
                                    <th >Temuan</th>
                                    <th >Catatan</th>
                                </tr>
                            </thead>

                            <tbody>
                                

                            </tbody>
                        </table>
                          </div>
                        </div>
                      </div>
                    </div>
