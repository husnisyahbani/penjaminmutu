<?php
/**
 * Daftar dokumen SPMI (Penetapan, Pelaksanaan, Evaluasi, Pengendalian,
 * Peningkatan) - tema halaman depan (homepage).
 *
 * Dipakai oleh 5 controller umum: Penetapan, Pelaksanaan, Evaluasi,
 * Pengendalian, Peningkatan.
 *
 * Data:
 *   $dataku : array baris (data_uraian, data_keterangan, data_file)
 *   $judul  : nama sub menu SPMI
 *   $home   : HomeModel (pengaturan warna/logo/teks halaman depan)
 */

$judul      = isset($judul) ? $judul : '';
$dataku     = isset($dataku) ? $dataku : array();
$nama_sistem = isset($home) ? $home->val('site_nama', 'SIJAMU') : 'SIJAMU';

$spmi_menu = array(
    array('slug' => 'penetapan', 'label' => 'Penetapan'),
    array('slug' => 'pelaksanaan', 'label' => 'Pelaksanaan'),
    array('slug' => 'evaluasi', 'label' => 'Evaluasi'),
    array('slug' => 'pengendalian', 'label' => 'Pengendalian'),
    array('slug' => 'peningkatan', 'label' => 'Peningkatan'),
);

/* Google tag (gtag.js) - sama seperti sebelumnya */
$gtag = '<script async src="' . base_url('assets/desain/js') . '"></script>'
      . '<script>'
      . 'window.dataLayer = window.dataLayer || [];'
      . 'function gtag(){dataLayer.push(arguments);}'
      . "gtag('js', new Date());"
      . "gtag('config', 'G-S75WL5XL9T');"
      . '</script>';

$this->load->view('umum/includes/hp_top', array(
    'title'      => 'Dokumen ' . $judul . ' - ' . $nama_sistem,
    'page'       => 'spmi',
    'home'       => $home,
    'head_extra' => $gtag,
));
?>

  <!-- ================= JUDUL + SUB MENU SPMI ================= -->
  <section class="hp-hero hp-hero--page">
    <div class="container">
      <span class="hp-hero__badge"><i class="bi bi-journal-text"></i> SPMI</span>

      <h1 class="hp-hero__title">
        Daftar Dokumen
        <span class="hp-hero__subjudul"><?php echo htmlspecialchars($judul); ?></span>
      </h1>

      <p class="hp-hero__text">
        Kumpulan dokumen siklus SPMI pada tahap <?php echo htmlspecialchars($judul); ?>.
        Klik <strong>Lihat</strong> untuk membuka pratinjau dokumen.
      </p>

      <!-- sub menu SPMI -->
      <nav class="hp-spmi-nav" aria-label="Sub menu SPMI">
        <?php foreach ($spmi_menu as $menu): ?>
          <a class="hp-spmi-nav__link<?php echo $menu['label'] === $judul ? ' is-active' : ''; ?>"
             href="<?php echo base_url($menu['slug']); ?>"
             <?php echo $menu['label'] === $judul ? 'aria-current="page"' : ''; ?>>
            <?php echo htmlspecialchars($menu['label']); ?>
          </a>
        <?php endforeach; ?>
      </nav>
    </div>
  </section>

  <!-- ================= TABEL DOKUMEN ================= -->
  <section class="hp-section hp-section--soft">
    <div class="container">

      <div class="hp-dokumen__meta" data-reveal>
        <span class="hp-dokumen__count">
          <i class="bi bi-files"></i> <?php echo count($dataku); ?> dokumen
        </span>
        <span class="text-secondary small">Siklus SPMI &mdash; <?php echo htmlspecialchars($judul); ?></span>
      </div>

      <?php if (empty($dataku)): ?>
        <div class="hp-empty" data-reveal>
          <i class="bi bi-inbox fs-3 d-block mb-3"></i>
          Belum ada dokumen pada tahap <?php echo htmlspecialchars($judul); ?>.
        </div>
      <?php else: ?>
        <div class="hp-table-wrap" data-reveal>
          <div class="table-responsive">
            <table class="hp-table">
              <thead>
                <tr>
                  <th style="width:70px">No</th>
                  <th>Uraian</th>
                  <th>Keterangan</th>
                  <th style="width:130px">File</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($dataku as $index => $out): ?>
                  <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $out['data_uraian']; ?></td>
                    <td><?php echo $out['data_keterangan']; ?></td>
                    <td>
                      <a href="javascript:void(0)"
                         class="hp-dl preview-pdf"
                         data-file="<?php echo base_url('filedata/' . $out['data_file']); ?>"
                         data-title="<?php echo htmlspecialchars($out['data_uraian'], ENT_QUOTES); ?>">
                        <i class="bi bi-eye"></i> Lihat
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </section>

  <!-- ================= PRATINJAU PDF ================= -->
  <div class="modal fade" id="pdfPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content rounded-4">

        <div class="modal-header">
          <h5 class="modal-title" id="pdfModalTitle">Preview Dokumen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body bg-light">
          <div id="pdfContainer" class="d-flex flex-column align-items-center gap-4"></div>
        </div>

      </div>
    </div>
  </div>

<?php
/* Skrip pratinjau PDF ditangkap dulu, lalu disisipkan hp_bottom (sebelum </body>). */
ob_start();
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
const pdfjsLib = window['pdfjs-dist/build/pdf'];

pdfjsLib.GlobalWorkerOptions.workerSrc =
  'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

document.querySelectorAll('.preview-pdf').forEach(btn => {
  btn.addEventListener('click', function () {

    const title = this.dataset.title;
    const modalTitle = document.getElementById('pdfModalTitle');

    modalTitle.textContent = title;

    const url = this.dataset.file;
    const container = document.getElementById('pdfContainer');

    // Bersihkan halaman lama
    container.innerHTML = '';

    // Tampilkan modal
    const modal = new bootstrap.Modal(
      document.getElementById('pdfPreviewModal')
    );
    modal.show();

    pdfjsLib.getDocument(url).promise.then(pdf => {

      for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
        pdf.getPage(pageNum).then(page => {

          const scale = 1.3;
          const viewport = page.getViewport({ scale });

          const canvas = document.createElement('canvas');
          const context = canvas.getContext('2d');

          canvas.width = viewport.width;
          canvas.height = viewport.height;

          canvas.classList.add('shadow-sm', 'rounded');
          container.appendChild(canvas);

          page.render({
            canvasContext: context,
            viewport: viewport
          });

        });
      }

    });
  });
});
</script>
<?php
$tail = ob_get_clean();

$this->load->view('umum/includes/hp_bottom', array(
    'home' => $home,
    'tail' => $tail,
));

