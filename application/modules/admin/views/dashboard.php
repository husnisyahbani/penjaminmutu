<?php
/**
 * Dashboard
 *
 * Tata letak: kartu statistik di bagian atas, grafik batang di tengah,
 * statistik pendukung di sisi kiri dan kanan.
 *
 * @var array $stat_utama      angka utama
 * @var array $stat_kategori   jumlah dokumen per kategori
 * @var array $grafik_audit    data grafik status audit (label, nilai, warna)
 * @var array $dokumen_terbaru lima dokumen mutu terbaru
 * @var array $stat_audit      status audit mutu internal
 * @var array $stat_pengguna   jumlah pengguna per peran
 * @var array $stat_dokumen    dokumen tampil / disembunyikan
 */
if (!function_exists('dsh_angka')) {
    /**
     * Format angka dengan pemisah ribuan.
     */
    function dsh_angka($angka)
    {
        return number_format((int) $angka, 0, ',', '.');
    }
}

if (!function_exists('dsh_potong')) {
    /**
     * Potong teks panjang (pengganti mb_strimwidth agar aman tanpa mbstring).
     */
    function dsh_potong($teks, $panjang = 40)
    {
        $teks = trim((string) $teks);

        if (strlen($teks) <= $panjang) {
            return $teks;
        }

        return rtrim(substr($teks, 0, $panjang - 1)) . '…';
    }
}

$kartu = array(
    array(
        'judul' => 'Dokumen Mutu',
        'nilai' => $stat_utama['dokumen'],
        'ikon'  => 'md-library',
        'warna' => '#3949ab',
        'ket'   => 'Seluruh dokumen standar mutu',
        'tautan' => base_url('admin/data'),
    ),
    array(
        'judul' => 'Audit Mutu Internal',
        'nilai' => $stat_utama['audit'],
        'ikon'  => 'md-assignment-check',
        'warna' => '#00897b',
        'ket'   => $stat_audit['selesai']['jumlah'] . ' audit selesai',
        'tautan' => base_url('admin/daftaraudit'),
    ),
    array(
        'judul' => 'Surat Keputusan',
        'nilai' => $stat_utama['sk'],
        'ikon'  => 'md-file-text',
        'warna' => '#1e88e5',
        'ket'   => 'SK penjaminan mutu',
        'tautan' => base_url('admin/sk'),
    ),
    array(
        'judul' => 'Berita & Pengumuman',
        'nilai' => $stat_utama['publikasi'],
        'ikon'  => 'md-rss',
        'warna' => '#fb8c00',
        'ket'   => $stat_utama['berita'] . ' berita, ' . $stat_utama['pengumuman'] . ' pengumuman',
        'tautan' => base_url('admin/berita'),
    ),
);

$total_kategori = 0;

foreach ($stat_kategori as $bagian) {
    $total_kategori += $bagian['jumlah'];
}
?>

<style type="text/css">
  /* Kartu statistik dashboard: warna diberikan langsung (inline) supaya
     selalu tampil, tidak bergantung kelas utility tema. */
  .dsh-kartu {
    display: block;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 6px;
    color: #fff !important;
    text-decoration: none !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, .14);
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .dsh-kartu:hover { transform: translateY(-2px); box-shadow: 0 7px 16px rgba(0, 0, 0, .2); }
  .dsh-kartu .dsh-ikon { float: left; font-size: 32px; line-height: 1; margin: 2px 16px 22px 0; opacity: .9; }
  .dsh-kartu .dsh-angka { font-size: 24px; font-weight: 600; line-height: 1.2; }
  .dsh-kartu .dsh-judul { font-size: 11px; letter-spacing: .08em; text-transform: uppercase; opacity: .92; }
  .dsh-kartu .dsh-ket { font-size: 12px; opacity: .78; margin-top: 2px; }
</style>

    <!-- Page -->
    <div class="page">

      <div class="page-content container-fluid">

        <!-- ==================== STATISTIK UTAMA (ATAS) ==================== -->
        <div class="row">
          <?php foreach ($kartu as $k): ?>
            <div class="col-xl-3 col-md-6">
              <a class="dsh-kartu" href="<?php echo $k['tautan']; ?>"
                 style="background-color:<?php echo $k['warna']; ?>;">
                <i class="icon <?php echo $k['ikon']; ?> dsh-ikon" aria-hidden="true"></i>
                <div style="overflow:hidden;">
                  <div class="dsh-angka"><?php echo dsh_angka($k['nilai']); ?></div>
                  <div class="dsh-judul"><?php echo $k['judul']; ?></div>
                  <div class="dsh-ket"><?php echo $k['ket']; ?></div>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="row">

          <!-- ==================== SISI KIRI ==================== -->
          <div class="col-xl-3 col-md-6">
            <?php /* Dokumen mutu per kategori: statistik samping (bukan grafik) */ ?>
            <div class="panel">
              <header class="panel-heading">
                <h3 class="panel-title">Dokumen Mutu per Kategori</h3>
                <div class="panel-actions panel-actions-keep">
                  <span class="badge badge-default"><?php echo dsh_angka($stat_utama['dokumen']); ?> dokumen</span>
                </div>
              </header>
              <div class="panel-body pt-0">
                <table class="table table-hover w-full mb-0">
                  <tbody>
                    <?php foreach ($stat_kategori as $bagian): ?>
                      <tr>
                        <td style="border-top:none;">
                          <span style="display:inline-block;width:10px;height:10px;border-radius:2px;margin-right:8px;background:<?php echo $bagian['warna']; ?>;"></span>
                          <?php echo $bagian['label']; ?>
                        </td>
                        <td class="text-right font-weight-600" style="border-top:none;"><?php echo dsh_angka($bagian['jumlah']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                    <tr>
                      <td class="font-weight-600">Total</td>
                      <td class="text-right font-weight-600"><?php echo dsh_angka($stat_utama['dokumen']); ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="panel">
              <header class="panel-heading">
                <h3 class="panel-title">Status Dokumen</h3>
              </header>
              <div class="panel-body pt-0">
                <table class="table table-hover w-full mb-0">
                  <tbody>
                    <tr>
                      <td style="border-top:none;">Ditampilkan di halaman depan</td>
                      <td class="text-right font-weight-600" style="border-top:none;"><?php echo dsh_angka($stat_dokumen['tampil']); ?></td>
                    </tr>
                    <tr>
                      <td>Disembunyikan</td>
                      <td class="text-right font-weight-600"><?php echo dsh_angka($stat_dokumen['sembunyi']); ?></td>
                    </tr>
                    <tr>
                      <td class="font-weight-600">Total</td>
                      <td class="text-right font-weight-600"><?php echo dsh_angka($stat_dokumen['total']); ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- ==================== GRAFIK BATANG (TENGAH) ==================== -->
          <div class="col-xl-6 col-md-12">
            <div class="panel">
              <header class="panel-heading">
                <h3 class="panel-title">Status Audit Mutu Internal</h3>
                <div class="panel-actions panel-actions-keep">
                  <span class="badge badge-default"><?php echo dsh_angka($stat_utama['audit']); ?> audit</span>
                </div>
              </header>
              <div class="panel-body">
                <div style="position:relative;height:340px;">
                  <canvas id="grafikAudit"></canvas>
                </div>
              </div>
              <div class="panel-body pt-0">
                <p class="mb-0" style="opacity:.65;">
                  Jumlah audit mutu internal menurut statusnya:
                  <?php
                  $potong_status = array();
                  foreach ($stat_audit as $status) {
                      $potong_status[] = $status['label'] . ' ' . dsh_angka($status['jumlah']);
                  }
                  echo implode(' &middot; ', $potong_status);
                  ?>.
                </p>
              </div>
            </div>
          </div>

          <!-- ==================== SISI KANAN ==================== -->
          <div class="col-xl-3 col-md-6">
            <div class="panel">
              <header class="panel-heading">
                <h3 class="panel-title">Pengguna Sistem</h3>
                <div class="panel-actions panel-actions-keep">
                  <span class="badge badge-default"><?php echo dsh_angka($stat_utama['pengguna']); ?> akun</span>
                </div>
              </header>
              <div class="panel-body pt-0">
                <table class="table table-hover w-full mb-0">
                  <tbody>
                    <?php
                    $label_peran = array(
                        'PPM'     => 'PPM',
                        'AUDITOR' => 'Auditor',
                        'AUDITEE' => 'Auditee',
                    );
                    foreach ($stat_pengguna as $peran => $jumlah): ?>
                      <tr>
                        <td style="border-top:none;"><?php echo isset($label_peran[$peran]) ? $label_peran[$peran] : $peran; ?></td>
                        <td class="text-right font-weight-600" style="border-top:none;"><?php echo dsh_angka($jumlah); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="panel">
              <header class="panel-heading">
                <h3 class="panel-title">Dokumen Mutu Terbaru</h3>
                <div class="panel-actions panel-actions-keep">
                  <a class="btn btn-sm btn-outline btn-default" href="<?php echo base_url('admin/data'); ?>">Semua</a>
                </div>
              </header>
              <div class="panel-body pt-0">
                <?php if (empty($dokumen_terbaru)): ?>
                  <p class="mb-0" style="opacity:.65;">Belum ada dokumen mutu.</p>
                <?php else: ?>
                  <table class="table table-hover w-full mb-0">
                    <tbody>
                      <?php foreach ($dokumen_terbaru as $dok): ?>
                        <tr>
                          <td style="border-top:none;">
                            <?php echo htmlspecialchars(dsh_potong($dok['data_uraian'], 42)); ?>
                            <div style="font-size:11px;opacity:.65;">
                              <?php echo htmlspecialchars($dok['data_kategori']); ?>
                              <?php if (!empty($dok['data_create'])): ?>
                                &middot; <?php echo date('d/m/Y', strtotime($dok['data_create'])); ?>
                              <?php endif; ?>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                <?php endif; ?>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
    <!-- End Page -->

    <script type="text/javascript">
      window.DASHBOARD_AUDIT = <?php echo json_encode($grafik_audit, JSON_UNESCAPED_UNICODE); ?>;
    </script>
