<div class="page">
    <div class="page-content container-fluid">
        
        <?php
        /* Kartu statistik temuan - memakai komponen bersama
           assets/app/kartu-statistik.css (seragam dengan dashboard auditee,
           admin, dan daftar audit auditor). Tiap kartu kategori menampilkan
           porsi temuan terhadap total, dan kartu total menampilkan berapa
           temuan yang belum punya rencana koreksi. */
        $total = isset($total_temuan) ? (int) $total_temuan : 0;
        $siap  = isset($sudah_koreksi) ? (int) $sudah_koreksi : 0;
        $sisa  = isset($tanpa_koreksi) ? (int) $tanpa_koreksi : 0;

        $persen = function ($nilai) use ($total) {
            if ($total < 1) {
                return 0;
            }
            return (int) round($nilai * 100 / $total);
        };

        $kartu_temuan = array(
            array(
                'judul'  => 'Total Temuan',
                'nilai'  => $total,
                'ikon'   => 'md-assignment',
                'warna'  => '#3949ab',
                'ket'    => $total > 0
                    ? $siap . ' sudah ditindaklanjuti, ' . $sisa . ' belum'
                    : 'Belum ada temuan pada unit Anda',
                'bar'    => $total > 0 ? $persen($siap) : 0,
                'bar_ket' => $total > 0 ? $persen($siap) . '% sudah ada rencana koreksi' : '',
            ),
            array(
                'judul'  => 'Observasi',
                'nilai'  => isset($observasi) ? (int) $observasi : 0,
                'ikon'   => 'md-info-outline',
                'warna'  => '#1e88e5',
                'ket'    => 'Catatan perbaikan (OB)',
                'bar'    => $persen(isset($observasi) ? (int) $observasi : 0),
                'bar_ket' => $persen(isset($observasi) ? (int) $observasi : 0) . '% dari total temuan',
            ),
            array(
                'judul'  => 'Minor',
                'nilai'  => isset($minor) ? (int) $minor : 0,
                'ikon'   => 'md-alert-triangle',
                'warna'  => '#fb8c00',
                'ket'    => 'Ketidaksesuaian minor (TS MINOR)',
                'bar'    => $persen(isset($minor) ? (int) $minor : 0),
                'bar_ket' => $persen(isset($minor) ? (int) $minor : 0) . '% dari total temuan',
            ),
            array(
                'judul'  => 'Mayor',
                'nilai'  => isset($mayor) ? (int) $mayor : 0,
                'ikon'   => 'md-alert-octagon',
                'warna'  => '#e53935',
                'ket'    => 'Ketidaksesuaian mayor (TS MAYOR)',
                'bar'    => $persen(isset($mayor) ? (int) $mayor : 0),
                'bar_ket' => $persen(isset($mayor) ? (int) $mayor : 0) . '% dari total temuan',
            ),
        );
        ?>

        <div class="row" data-plugin="matchHeight" data-by-row="true">

          <?php foreach ($kartu_temuan as $k): ?>
            <div class="col-xl-3 col-md-6">
              <div class="kartu-stat" style="background-color:<?php echo $k['warna']; ?>;">
                <i class="icon <?php echo $k['ikon']; ?> kartu-stat__ikon" aria-hidden="true"></i>
                <div class="kartu-stat__isi">
                  <div class="kartu-stat__angka"><?php echo number_format($k['nilai'], 0, ',', '.'); ?></div>
                  <div class="kartu-stat__judul"><?php echo $k['judul']; ?></div>
                  <div class="kartu-stat__ket"><?php echo $k['ket']; ?></div>
                  <?php if ($k['bar_ket'] !== ''): ?>
                  <div class="kartu-stat__bar" role="img" aria-label="<?php echo html_escape($k['bar_ket']); ?>">
                    <span class="kartu-stat__bar-isi" style="width:<?php echo (int) $k['bar']; ?>%;"></span>
                  </div>
                  <div class="kartu-stat__bar-ket"><?php echo $k['bar_ket']; ?></div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>

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
