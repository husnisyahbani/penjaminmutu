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

            <div class="col-xl-4 col-md-8">
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

            <div class="col-xl-4 col-md-8">
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
                    <header class="panel-heading panel-heading-filter">
                        <div class="panel-heading-isi">
                            <h3 class="panel-title"><?=$title?></h3>
                            <div class="panel-aksi">
                                <div class="filter-kotak">
                                    <label for="cari_ptk"><i class="icon md-search" aria-hidden="true"></i>Cari</label>
                                    <input type="text" class="form-control" id="cari_ptk"
                                           placeholder="Cari formulir, lingkup, catatan, koreksi">
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="ptk-tabel-kotak">
                            <table class="table table-hover dataTable ptk-tabel" id="ptk">
                                <thead>
                                    <tr>
                                        <th class="ptk-kolom-no">No</th>
                                        <th class="ptk-kolom-formulir">Formulir</th>
                                        <th class="ptk-kolom-butir">Butir Lingkup</th>
                                        <th class="ptk-kolom-hasil">Hasil</th>
                                        <th class="ptk-kolom-temuan">Temuan</th>
                                        <th class="ptk-kolom-catatan">Catatan</th>
                                        <th class="ptk-kolom-koreksi">Rencana Koreksi</th>
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
</div>




<div
    class="modal fade"
    id="editModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formedit" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="exampleFormModalLabel">Rencana Koreksi</h4>
            </div>
            <div class="modal-body">

                <input type="hidden" id="ptk_lingkup_id" name="lingkup_id"/>
                <input type="hidden" id="ptk_dtform_id" name="dtform_id"/>

                <div class="row">
                    <div class="col-md-12">
                        <h4 class="example-title">Butir Lingkup</h4>
                        <div id="ptk_butir" class="ptk-teks"></div>
                    </div>
                    <div class="col-md-12">
                        <h4 class="example-title">Hasil</h4>
                        <div id="ptk_hasil" class="ptk-teks"></div>
                    </div>
                    <div class="col-md-12">
                        <h4 class="example-title">Catatan</h4>
                        <div id="ptk_catatan" class="ptk-teks"></div>
                    </div>
                    <div class="col-md-12">
                        <h4 class="example-title">Rencana Koreksi</h4>
                        <textarea id="ptk_koreksi" class="form-control ptk-koreksi-input" name="ptk_koreksi"
                                  rows="6" placeholder="Tulis rencana koreksi untuk butir ini..."></textarea>
                        <div class="ptk-rencana-info">Tulis teks biasa; enter dipakai untuk memisahkan poin.</div>
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
