<div class="page">
    <div class="page-content container-fluid">
        
        <div class="row" data-plugin="matchHeight" data-by-row="true">

            <div class="col-xl-4 col-md-8">
                <div class="card card-block p-25 bg-red-600">
                    <div class="counter counter-lg counter-inverse">
                        <div class="counter-label text-uppercase">AUDIT BELUM SELESAI</div>
                        <div class="counter-number-group">
                            <span class="counter-number-related"></span>
                            <span class="counter-number">
                            <?php if(isset($totalbelumselesai)){echo number_format($totalbelumselesai,0,',','.');}else{echo '0';}?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-8">
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

            <div class="col-xl-4 col-md-8">
                <div class="card card-block p-25 bg-blue-600">
                    <div class="counter counter-lg counter-inverse">
                        <div class="counter-label text-uppercase">TOTAL AUDIT</div>
                        <div class="counter-number-group">
                            <span class="counter-number-related"></span>
                            <span class="counter-number">
                            <?php if(isset($total)){echo number_format($total,0,',','.');}else{echo '0';}?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row"  data-by-row="true">
            <div class="col-xl-4 col-md-8">
            <div class="panel">
            <div class="card">
                <div class="card-header white bg-red-600 p-30 clearfix">
                    <a class="avatar avatar-100 float-left mr-20" href="javascript:void(0)">
                        <img src="<?php echo base_url("filedata/logostikfinal.png");?>" alt=""></a>
                        <div class="float-left">
                            <div class="font-size-20 mb-15"><?php if(isset($auditor->nama)){echo $auditor->nama;}?></div>
                            <p class="mb-5 text-nowrap">
                                <i class="icon md-book mr-10" aria-hidden="true"></i>
                                <span class="text-break"><?php if(isset($auditor->unitkerja)){echo $auditor->unitkerja;}?></span>
                            </p>
                            <p class="mb-5 text-nowrap">
                                <i class="icon md-book mr-10" aria-hidden="true"></i>
                                <span class="text-break"><?php if(isset($auditor->role)){echo $auditor->role;}?></span>
                            </p>
                            
                            
                        </div>
                    </div>
            </div>
</div>
            </div>
            <div class="col-xl-8 col-md-16">

                <div class="panel">
                    <header class="panel-heading">
                        <h3 class="panel-title">AUDIT</h3>
                        <div class="panel-actions panel-actions-keep">
                            
                            <button type="button" class="btn btn-sm btn-icon btn-success" id="tambah">
                                <i class="icon md-plus" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </header>
                    <div class="panel-body">
                        <table class="table table-hover dataTable w-full" id="auditor">
                            <thead>
                                <tr>
                                    <th >No</th>
                                    <th >Tanggal</th>
                                    <th >Formulir</th>
                                    <th >Auditee</th>
                                    <th> Download</th>
                                    <th> Detail</th>
                                </tr>
                            </thead>

                            <tbody>
                            

                            </tbody>
                        </table>
                    </div>
                </div>

                

            </div>


            <div class="col-xl-12 col-md-24">
                <div class="panel">
                    <header class="panel-heading">
                        <h3 class="panel-title">Daftar Pertanyaan</h3>
                        <div class="panel-actions panel-actions-keep">
                            
                        </div>
                    </header>
                    <div class="panel-body">
                        <table class="table table-hover dataTable w-full" id="detail">
                            <thead>
                                <tr>
                                    <th >No</th>
                                    <th >Tujuan</th>
                                    <th >Pertanyaan</th>
                                    <th >Ruang Lingkup</th>
                                    <th >Hasil Observasi</th>
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
    </div>
</div>

<div
    class="modal fade"
    id="auditorModal"
    aria-hidden="false"
    aria-labelledby="exampleFormModalLabel"
    role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form id="formauditor" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="exampleFormModalLabel">AUDIT</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 center">
                        <select class="form-control" name="jp_id" id="jp_id" data-fv-notempty="true"
                        data-fv-notempty-message="Wajib Dipilih">                                 
                            <option value="">-- Pilih Formulir Audit --</option>
                            <?php
                                foreach($audit as $row){
                                    echo '<option value="'.$row->form_id.'">'.$row->form_nama.'</option>';
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
