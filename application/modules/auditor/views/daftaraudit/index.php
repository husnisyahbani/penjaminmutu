<div class="page">
    <div class="page-content container-fluid">
        
        <?php
        /* Kartu statistik - memakai gaya yang sama dengan dashboard admin
           (assets/app/kartu-statistik.css). */
        $kartu_audit = array(
            array(
                'judul' => 'Audit Draft',
                'nilai' => isset($totaldraft) ? $totaldraft : 0,
                'ikon'  => 'md-edit',
                'warna' => '#90a4ae',
                'ket'   => 'Belum diajukan',
            ),
            array(
                'judul' => 'Audit Terkirim',
                'nilai' => isset($totalterkirim) ? $totalterkirim : 0,
                'ikon'  => 'md-play-circle',
                'warna' => '#1e88e5',
                'ket'   => 'Menunggu diproses',
            ),
            array(
                'judul' => 'Audit Proses',
                'nilai' => isset($totalproses) ? $totalproses : 0,
                'ikon'  => 'md-refresh',
                'warna' => '#fb8c00',
                'ket'   => 'Sedang dinilai',
            ),
            array(
                'judul' => 'Audit Selesai',
                'nilai' => isset($totalselesai) ? $totalselesai : 0,
                'ikon'  => 'md-check-circle',
                'warna' => '#43a047',
                'ket'   => 'Penilaian tuntas',
            ),
        );
        ?>

        <div class="row" data-plugin="matchHeight" data-by-row="true">

          <?php foreach ($kartu_audit as $k): ?>
            <div class="col-xl-3 col-md-6">
              <div class="kartu-stat" style="background-color:<?php echo $k['warna']; ?>;">
                <i class="icon <?php echo $k['ikon']; ?> kartu-stat__ikon" aria-hidden="true"></i>
                <div style="overflow:hidden;">
                  <div class="kartu-stat__angka"><?php echo number_format($k['nilai'], 0, ',', '.'); ?></div>
                  <div class="kartu-stat__judul"><?php echo $k['judul']; ?></div>
                  <div class="kartu-stat__ket"><?php echo $k['ket']; ?></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>

        </div>
        <div class="row"  data-by-row="true">
            
            <div class="col-xl-12 col-md-24">

                <div class="panel">
                    <header class="panel-heading">
                        <h3 class="panel-title">Daftar Audit</h3>
                        <div class="panel-actions panel-actions-keep">
                            
                            <button type="button" class="btn btn-success" id="tambah">
                                <i class="icon md-plus" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </header>
                    <div class="panel-body">
                        <table class="table table-hover dataTable w-full" id="daftaraudit">
                            <thead>
                                <tr>
                                    <th width="1%">No</th>
                                    <th>Formulir</th>
                                    <th>Auditor</th>
                                    <th>Auditee</th>
                                    <th>Unit</th>
                                    <th width="1%" class="tabel-aksi-sel">Aksi</th>
                                    <th width="1%" class="tabel-aksi-sel">Status</th>
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

