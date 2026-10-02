<div class="page">
  <div class="page-content container-fluid">

    <?php
    /* Halaman delik auditee - susunannya mengikuti halaman PTK:
       kartu ringkasan di atas, lalu tabel butir. Tab lama dihapus.

       Yang ditampilkan: seluruh butir pertanyaan yang dipilih yang sudah
       dinilai auditor (S / OB / TS MINOR / TS MAYOR), tanpa penyaringan
       jenis penilaian - butir "S" (sesuai) ikut tampil. Data dari
       controller: $butir (AuditjawabModel::butirDinilai). */
    $ringkas = isset($ringkas) ? $ringkas : array(
        'total' => count($butir), 'sesuai' => 0, 'observasi' => 0, 'minor' => 0, 'mayor' => 0,
    );
    $jml_dinilai = (int) $ringkas['total'];
    $jml_lingkup = isset($jml_lingkup) ? (int) $jml_lingkup : $jml_dinilai;
    $jml_temuan  = (int) $ringkas['observasi'] + (int) $ringkas['minor'] + (int) $ringkas['mayor'];

    /* Porsi terhadap butir yang sudah dinilai (dipakai bar kartu kategori). */
    $persen = function ($nilai) use ($jml_dinilai) {
        if ($jml_dinilai < 1) {
            return 0;
        }
        return (int) round($nilai * 100 / $jml_dinilai);
    };

    $kartu = array(
        array(
            'judul'   => 'Butir Dinilai',
            'nilai'   => $jml_dinilai,
            'ikon'    => 'md-assignment',
            'warna'   => '#3949ab',
            'ket'     => $jml_lingkup > 0
                ? 'dari ' . $jml_lingkup . ' butir lingkup pertanyaan ini'
                : 'Belum ada butir lingkup pada pertanyaan ini',
            'bar'     => $jml_lingkup > 0 ? (int) round($jml_dinilai * 100 / $jml_lingkup) : 0,
            'bar_ket' => $jml_lingkup > 0
                ? (int) round($jml_dinilai * 100 / $jml_lingkup) . '% butir sudah dinilai auditor'
                : '',
        ),
        array(
            'judul'   => 'Sesuai',
            'nilai'   => (int) $ringkas['sesuai'],
            'ikon'    => 'md-check-circle',
            'warna'   => '#43a047',
            'ket'     => 'Memenuhi kriteria (S)',
            'bar'     => $persen((int) $ringkas['sesuai']),
            'bar_ket' => $persen((int) $ringkas['sesuai']) . '% dari butir dinilai',
        ),
        array(
            'judul'   => 'Observasi',
            'nilai'   => (int) $ringkas['observasi'],
            'ikon'    => 'md-info-outline',
            'warna'   => '#1e88e5',
            'ket'     => 'Catatan perbaikan (OB)',
            'bar'     => $persen((int) $ringkas['observasi']),
            'bar_ket' => $persen((int) $ringkas['observasi']) . '% dari butir dinilai',
        ),
        array(
            'judul'   => 'Minor',
            'nilai'   => (int) $ringkas['minor'],
            'ikon'    => 'md-alert-triangle',
            'warna'   => '#fb8c00',
            'ket'     => 'Ketidaksesuaian minor (TS MINOR)',
            'bar'     => $persen((int) $ringkas['minor']),
            'bar_ket' => $persen((int) $ringkas['minor']) . '% dari butir dinilai',
        ),
        array(
            'judul'   => 'Mayor',
            'nilai'   => (int) $ringkas['mayor'],
            'ikon'    => 'md-alert-octagon',
            'warna'   => '#e53935',
            'ket'     => 'Ketidaksesuaian mayor (TS MAYOR)',
            'bar'     => $persen((int) $ringkas['mayor']),
            'bar_ket' => $persen((int) $ringkas['mayor']) . '% dari butir dinilai',
        ),
    );
    ?>

    <!-- Informasi pertanyaan yang dipilih -->
    <div class="row" data-by-row="true">
      <div class="col-xl-12">
        <div class="panel">
          <header class="panel-heading panel-heading-filter">
            <div class="panel-heading-isi">
              <h3 class="panel-title">Informasi Pertanyaan</h3>
              <div class="panel-aksi">
                <div class="panel-aksi__tombol">
                  <button type="button" class="btn btn-sm btn-icon btn-warning"
                          audit_id="<?php echo (int) $audit_id; ?>" id="kembali">
                    <i class="icon md-undo" aria-hidden="true"></i>Kembali
                  </button>
                </div>
              </div>
            </div>
          </header>
          <div class="panel-body">
            <div class="topik-ringkas mb-15">
              <?php if (!empty($result['form_nama'])): ?>
              <span class="badge badge-primary"><?php echo html_escape($result['form_nama']); ?></span>
              <?php endif; ?>
              <?php if (isset($soal['dtform_urut']) && (int) $soal['dtform_urut'] > 0): ?>
              <span class="badge badge-info">Pertanyaan ke-<?php echo (int) $soal['dtform_urut']; ?></span>
              <?php endif; ?>
              <span class="badge badge-default"><?php echo $jml_lingkup; ?> butir lingkup</span>
              <?php if (!empty($result['unit'])): ?>
              <span class="badge badge-default"><?php echo html_escape($result['unit']); ?></span>
              <?php endif; ?>
              <?php if (!empty($result['auditor'])): ?>
              <span class="badge badge-default">Auditor: <?php echo html_escape($result['auditor']); ?></span>
              <?php endif; ?>
            </div>

            <?php if (isset($soal['dtform_pertanyaan']) && trim($soal['dtform_pertanyaan']) !== ''): ?>
            <div class="pertanyaan-isi"><?php echo $soal['dtform_pertanyaan']; ?></div>
            <?php endif; ?>

            <?php if (isset($soal['dtform_tujuan']) && trim($soal['dtform_tujuan']) !== ''): ?>
            <div class="aktivitas-bukti">
              <span class="aktivitas-label">Tujuan:</span>
              <?php echo nl2br(html_escape(lingkup_bersihkan($soal['dtform_tujuan']))); ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($lingkup)): ?>
            <div class="pertanyaan-butir">
              <button type="button" class="btn btn-sm btn-default" data-toggle="collapse"
                      data-target="#butir_lingkup" aria-expanded="false" aria-controls="butir_lingkup">
                <i class="icon md-chevron-down" aria-hidden="true"></i>
                Lihat <?php echo $jml_lingkup; ?> butir lingkup pertanyaan ini
              </button>
              <div class="collapse pertanyaan-butir__daftar" id="butir_lingkup">
                <?php echo $lingkup; ?>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Kartu ringkasan penilaian pertanyaan ini -->
    <div class="row" data-plugin="matchHeight" data-by-row="true">
      <?php foreach ($kartu as $k): ?>
      <div class="col-12 col-md-6 col-xl-4 col-xxl">
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

    <!-- Daftar butir yang sudah dinilai pada pertanyaan ini -->
    <div class="row" data-by-row="true">
      <div class="col-xl-12">
        <div class="panel">
          <header class="panel-heading panel-heading-filter">
            <div class="panel-heading-isi">
              <h3 class="panel-title">Daftar Butir Dinilai</h3>
              <div class="panel-aksi">
                <div class="topik-ringkas">
                  <span class="badge badge-info"><?php echo $jml_dinilai; ?> butir ditampilkan</span>
                  <span class="badge badge-warning"><?php echo $jml_temuan; ?> bertemuan</span>
                </div>
              </div>
            </div>
          </header>
          <div class="panel-body">
            <?php if (empty($butir)): ?>
            <div class="topik-kosong">Belum ada butir yang dinilai pada pertanyaan ini.</div>
            <?php else: ?>
            <div class="ptk-tabel-kotak">
              <table class="table table-hover ptk-tabel butir-tabel">
                <thead>
                  <tr>
                    <th class="butir-kolom-no">No</th>
                    <th class="butir-kolom-isi">Butir Lingkup</th>
                    <th class="butir-kolom-hasil">Hasil</th>
                    <th class="butir-kolom-temuan">Temuan</th>
                    <th class="butir-kolom-catatan">Catatan</th>
                    <th class="butir-kolom-koreksi">Rencana Koreksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($butir as $i => $b):
                      $isi     = lingkup_bersihkan($b['dtjwb_pertanyaan']);
                      $hasil   = lingkup_bersihkan($b['dtjwb_hasil']);
                      $temuan  = strtoupper(trim((string) $b['dtjwb_temuan']));
                      $catatan = lingkup_bersihkan($b['dtjwb_catatan']);
                      $koreksi = lingkup_teks_baris($b['dtjwb_koreksi']);

                      /* Warna badge mengikuti jenis penilaian, supaya sama
                         jelasnya dengan kategori pada kartu di atas. */
                      $warna = 'badge-default';
                      if ($temuan === 'S') {
                          $warna = 'badge-success';
                      } elseif ($temuan === 'OB') {
                          $warna = 'badge-info';
                      } elseif ($temuan === 'TS MINOR') {
                          $warna = 'badge-warning';
                      } elseif ($temuan === 'TS MAYOR') {
                          $warna = 'badge-danger';
                      }
                  ?>
                  <tr>
                    <td class="butir-kolom-no"><?php echo $i + 1; ?></td>
                    <td>
                      <div class="butir-klamp"><?php echo html_escape($isi); ?></div>
                    </td>
                    <td><div class="butir-klamp"><?php echo html_escape($hasil); ?></div></td>
                    <td class="text-center">
                      <span class="badge <?php echo $warna; ?>"><?php echo html_escape($temuan); ?></span>
                    </td>
                    <td><div class="butir-klamp"><?php echo html_escape($catatan); ?></div></td>
                    <td>
                      <?php if ($koreksi !== ''): ?>
                      <div class="butir-klamp"><?php echo nl2br(html_escape($koreksi)); ?></div>
                      <?php elseif ($temuan === 'S'): ?>
                      <span class="text-muted">Tidak perlu koreksi</span>
                      <?php else: ?>
                      <span class="butir-koreksi-kosong">Belum ada rencana koreksi</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
