<div class="page">
    <div class="page-content container-fluid">
        
        <div class="row" data-plugin="matchHeight" data-by-row="true">

            <div class="col-xl-4 col-md-8">
                <div class="card card-block p-25 bg-green-600">
                    <div class="counter counter-lg counter-inverse">
                        <div class="counter-label text-uppercase">OBSERVASI</div>
                        <div class="counter-number-group">
                            <span class="counter-number-related"></span>
                            <span class="counter-number">
                            <?php if(isset($observasi)){echo number_format($observasi,0,',','.');}else{echo '0';}?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-block p-25 bg-orange-600">
                    <div class="counter counter-lg counter-inverse">
                        <div class="counter-label text-uppercase">MINOR</div>
                        <div class="counter-number-group">
                            <span class="counter-number-related"></span>
                            <span class="counter-number">
                            <?php if(isset($minor)){echo number_format($minor,0,',','.');}else{echo '0';}?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-block p-25 bg-red-600">
                    <div class="counter counter-lg counter-inverse">
                        <div class="counter-label text-uppercase">MAYOR</div>
                        <div class="counter-number-group">
                            <span class="counter-number-related"></span>
                            <span class="counter-number">
                            <?php if(isset($mayor)){echo number_format($mayor,0,',','.');}else{echo '0';}?>
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
                        <h3 class="panel-title"><?=$title?></h3>
                        <div class="panel-actions panel-actions-keep">
                            
                            <!-- <button type="button" class="btn btn-sm btn-icon btn-success" id="tambah">
                                <i class="icon md-plus" aria-hidden="true"></i>Tambah
                            </button> -->
                        </div>
                    </header>
                    <div class="panel-body">
                        <table class="table table-hover dataTable w-full" id="ptk">
                            <thead>
                                <tr>
                                    <th >No</th>
                                    <th >Formulir</th>
                                    <th >Pertanyaan</th>
                                    <th >Hasil</th>
                                    <th >Temuan</th>
                                    <th> Catatan</th>
                                    <th> Rencana Koreksi</th>
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
                <h4 class="modal-title" id="exampleFormModalLabel">Rencana Koreksi</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                        <h4 class="example-title">Hasil</h4>
                        <textarea id="hasil" class="editor" name="hasil" readonly></textarea>
                    </div>
                    <div class="col-md-12 center">
                        <h4 class="example-title">Catatan</h4>
                        <textarea id="hasil" class="editor" name="hasil" readonly></textarea>
                    </div>
                    <div class="col-md-12 center">
                        <h4 class="example-title">Koreksi</h4>
                        <textarea id="ptk_koreksi" class="editor" name="ptk_koreksi"></textarea>
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