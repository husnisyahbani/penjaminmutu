<?php
/**
 * Detail audit (auditor) gaya halaman kursus:
 *   topik    = pertanyaan formulir
 *   activity = butir lingkup + hasil/temuan/catatan yang sudah diisi
 *
 * Tombol "Daftar Tilik" pada tiap topik membuka halaman pengisian seperti
 * semula (delik), sehingga alur pengisian tidak berubah.
 *
 * Data dari controller: $result, $audit_id, $topik.
 */
$potong = function ($teks, $maks = 140) {
    $t = lingkup_bersihkan($teks);
    if ($t === '') {
        return '';
    }
    if (hitung_potong($t) > $maks) {
        return mb_substr_potong($t, 0, $maks) . '…';
    }
    return $t;
};

$jml_pertanyaan = count($topik);
$jml_lingkup = 0;
$jml_dinilai = 0;
$jml_temuan = 0;
$jml_dijawab = 0;    // butir lingkup yang sudah dijawab auditee
$jml_belum = 0;
$jml_lampiran = 0;
foreach ($topik as $t) {
    $jml_lingkup   += (int) $t['jml_butir'];
    $jml_dinilai   += (int) $t['jml_dinilai'];
    $jml_temuan    += (int) $t['jml_temuan'];
    $jml_dijawab   += (int) $t['jml_dijawab'];
    $jml_belum     += isset($t['jml_belum']) ? (int) $t['jml_belum'] : 0;
    $jml_lampiran  += (int) $t['jml_lampiran'];
}
?>
<div class="page">
    <div class="page-content container-fluid">
        <div class="row" data-by-row="true">
            <div class="col-xl-12 col-md-24">
                <div class="panel">
                    <header class="panel-heading panel-heading-filter">
                        <div class="panel-heading-isi">
                            <h3 class="panel-title"><?php echo html_escape($result['form_nama']); ?></h3>
                            <div class="panel-aksi">
                                <div class="filter-kotak">
                                    <label for="cari_topik"><i class="icon md-search" aria-hidden="true"></i>Cari</label>
                                    <input type="text" class="form-control" id="cari_topik" placeholder="Cari pertanyaan atau lingkup">
                                </div>
                                <div class="panel-aksi__tombol">
                                    <a href="<?php echo base_url('auditor/daftaraudit'); ?>" class="btn btn-sm btn-default">
                                        <i class="icon md-undo" aria-hidden="true"></i>Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="panel-body">

                        <div class="topik-ringkas mb-15">
                            <span class="badge badge-info"><?php echo (int) $jml_pertanyaan; ?> pertanyaan</span>
                            <span class="badge badge-info"><?php echo (int) $jml_lingkup; ?> butir lingkup</span>
                            <span class="badge badge-success"><?php echo (int) $jml_dijawab; ?> dijawab</span>
                            <span class="badge badge-warning"><?php echo (int) $jml_belum; ?> belum dijawab</span>
                            <span class="badge badge-info"><?php echo (int) $jml_lampiran; ?> lampiran</span>
                            <span class="badge badge-primary"><?php echo (int) $jml_dinilai; ?> dinilai</span>
                            <span class="badge badge-warning"><?php echo (int) $jml_temuan; ?> temuan</span>
                        </div>

                        <div class="topik-berkas" id="topik_daftar">
                            <?php foreach ($topik as $i => $t):
                                $punya_lingkup = !empty($t['punya_lingkup']);
                                $cari = strtolower($t['teks'] . ' '
                                    . (isset($t['jwb']['jwb_jawaban']) ? lingkup_bersihkan($t['jwb']['jwb_jawaban']) : '') . ' '
                                    . implode(' ', array_map(function ($b) {
                                    return $b['lingkup_teks'] . ' ' . $b['jwb_jawaban'] . ' '
                                        . (string) $b['jwb_temuan'] . ' ' . lingkup_bersihkan($b['jwb_catatan']);
                                }, $t['butir'])));
                            ?>
                            <section class="topik" data-dtform_id="<?php echo (int) $t['dtform_id']; ?>"
                                     data-cari="<?php echo html_escape($cari); ?>">
                                <header class="topik-kepala">
                                    <span class="topik-nomor"><?php echo $i + 1; ?></span>
                                    <h4 class="topik-judul">
                                        <?php echo html_escape($t['teks']); ?>
                                        <span class="topik-info">
                                            <?php if ($punya_lingkup): ?>
                                            <span class="badge badge-info"><?php echo (int) $t['jml_butir']; ?> butir lingkup</span>
                                            <span class="badge <?php echo (int) $t['jml_belum'] === 0 ? 'badge-success' : 'badge-warning'; ?>">
                                                <?php echo (int) $t['jml_dijawab']; ?>/<?php echo (int) $t['jml_butir']; ?> dijawab
                                            </span>
                                            <?php else: ?>
                                            <span class="badge badge-default">tanpa butir lingkup</span>
                                            <span class="badge <?php echo !empty($t['sudah_dijawab']) ? 'badge-success' : 'badge-warning'; ?>">
                                                <?php echo !empty($t['sudah_dijawab']) ? 'sudah dijawab' : 'belum dijawab'; ?>
                                            </span>
                                            <?php endif; ?>
                                            <?php if ($t['jml_dinilai'] > 0): ?>
                                            <span class="badge badge-success"><?php echo (int) $t['jml_dinilai']; ?> dinilai</span>
                                            <?php endif; ?>
                                            <?php if ($t['jml_temuan'] > 0): ?>
                                            <span class="badge badge-warning"><?php echo (int) $t['jml_temuan']; ?> temuan</span>
                                            <?php endif; ?>
                                        </span>
                                    </h4>
                                    <i class="icon md-chevron-down topik-panah" aria-hidden="true"></i>
                                    <div class="topik-aksi">
                                        <button type="button" class="delik btn btn-sm btn-primary"
                                                dtform_id="<?php echo (int) $t['dtform_id']; ?>"
                                                audit_id="<?php echo (int) $audit_id; ?>">
                                            <i class="icon md-edit" aria-hidden="true"></i>Daftar Tilik
                                        </button>
                                    </div>
                                </header>
                                <div class="aktivitas-daftar">
                                    <?php if ($punya_lingkup && (int) $t['jml_belum'] > 0
                                            && isset($t['jwb']['jwb_jawaban'])
                                            && trim((string) $t['jwb']['jwb_jawaban']) !== ''): ?>
                                    <!-- Jawaban lama (tingkat pertanyaan) - tersimpan sebelum
                                         pertanyaan dipecah menjadi butir-butir lingkup -->
                                    <div class="aktivitas-bukti jawaban-lama">
                                        <span class="aktivitas-label">Jawaban lama (tingkat pertanyaan):</span>
                                        <?php echo nl2br(html_escape(lingkup_bersihkan($t['jwb']['jwb_jawaban']))); ?>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!$punya_lingkup): ?>
                                    <!-- Pertanyaan tanpa butir lingkup: jawaban tingkat pertanyaan
                                         (auditee mengisi di halaman detail audit) -->
                                    <div class="aktivitas-bukti">
                                        <span class="aktivitas-label">Jawaban auditee:</span>
                                        <?php
                                        $jawab_topik = isset($t['jwb']['jwb_jawaban']) ? $t['jwb']['jwb_jawaban'] : '';
                                        echo trim((string) $jawab_topik) !== ''
                                            ? nl2br(html_escape($potong($jawab_topik, 500)))
                                            : '<span class="text-muted">belum dijawab auditee</span>';
                                        ?>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($t['lampiran'])): ?>
                                    <!-- Lampiran jawaban auditee untuk pertanyaan ini -->
                                    <div class="aktivitas-bukti">
                                        <span class="aktivitas-label">Lampiran jawaban:</span>
                                        <div class="lampiran-daftar">
                                            <?php foreach ($t['lampiran'] as $l): ?>
                                            <div class="lampiran" data-lampiran_id="<?php echo (int) $l['lampiran_id']; ?>">
                                                <i class="icon md-file lampiran-ikon" aria-hidden="true"></i>
                                                <a class="lampiran-nama" target="_blank" rel="noopener"
                                                   href="<?php echo $l['url']; ?>"><?php echo html_escape($l['lampiran_asli']); ?></a>
                                                <span class="lampiran-ukuran text-muted"><?php echo html_escape($l['ukuran_teks']); ?></span>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php $nomor = 0; ?>
                                    <?php foreach ($t['butir'] as $b):
                                        $bisa_dijawab = !empty($b['bisa_dijawab']) && (int) $b['lingkup_id'] > 0;
                                        if ($bisa_dijawab) {
                                            $nomor++;
                                        }
                                        $jawaban = lingkup_bersihkan($b['jwb_jawaban']);
                                        $hasil  = $potong($b['jwb_hasil']);
                                        $temuan = trim((string) $b['jwb_temuan']);
                                        $catatan = $potong($b['jwb_catatan'], 180);
                                        $ada = ($hasil !== '' || $temuan !== '' || $catatan !== '');
                                    ?>
                                    <div class="aktivitas lingkup-item<?php echo $bisa_dijawab ? '' : ' lingkup-item--lama'; ?>"
                                         data-lingkup_id="<?php echo (int) $b['lingkup_id']; ?>"
                                         data-sudah-dijawab="<?php echo trim((string) $b['jwb_jawaban']) !== '' ? '1' : '0'; ?>"
                                         data-cari="<?php echo html_escape(strtolower($b['lingkup_teks'] . ' ' . $jawaban . ' ' . $hasil . ' ' . $temuan . ' ' . $catatan)); ?>">
                                        <i class="icon <?php echo $bisa_dijawab ? 'md-comment-text' : 'md-assignment'; ?> aktivitas-ikon" aria-hidden="true"></i>
                                        <div class="aktivitas-isi">
                                            <span class="aktivitas-teks">
                                                <?php if ($bisa_dijawab): ?>
                                                <span class="lingkup-nomor"><?php echo $nomor; ?>.</span>
                                                <?php endif; ?>
                                                <?php echo html_escape($b['lingkup_teks']); ?>
                                            </span>

                                            <?php if ($bisa_dijawab): ?>
                                            <!-- Jawaban auditee untuk butir lingkup ini -->
                                            <div class="aktivitas-bukti">
                                                <span class="aktivitas-label">Jawaban auditee:</span>
                                                <?php echo $jawaban !== ''
                                                    ? nl2br(html_escape($jawaban))
                                                    : '<span class="text-muted">belum dijawab</span>'; ?>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (!empty($b['lampiran'])): ?>
                                            <div class="lampiran-kotak">
                                                <span class="lampiran-judul">
                                                    <i class="icon md-attachment" aria-hidden="true"></i>Lampiran
                                                </span>
                                                <div class="lampiran-daftar">
                                                    <?php foreach ($b['lampiran'] as $l): ?>
                                                    <div class="lampiran" data-lampiran_id="<?php echo (int) $l['lampiran_id']; ?>">
                                                        <i class="icon md-file lampiran-ikon" aria-hidden="true"></i>
                                                        <a class="lampiran-nama" target="_blank" rel="noopener"
                                                           href="<?php echo $l['url']; ?>"><?php echo html_escape($l['lampiran_asli']); ?></a>
                                                        <span class="lampiran-ukuran text-muted"><?php echo html_escape($l['ukuran_teks']); ?></span>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (!$ada): ?>
                                            <div class="aktivitas-status">
                                                <span class="text-muted">belum dinilai</span>
                                            </div>
                                            <?php else: ?>
                                            <div class="aktivitas-status">
                                                <?php if ($temuan !== ''): ?>
                                                <span class="badge badge-warning"><?php echo html_escape($temuan); ?></span>
                                                <?php endif; ?>
                                                <?php if ($hasil !== ''): ?>
                                                <span class="badge badge-primary">Hasil: <?php echo html_escape($hasil); ?></span>
                                                <?php endif; ?>
                                                <?php if ($catatan !== ''): ?>
                                                <span class="badge badge-info">Catatan</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($catatan !== ''): ?>
                                            <div class="aktivitas-bukti">
                                                <span class="aktivitas-label">Catatan:</span> <?php echo html_escape($catatan); ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>

                                    <?php if (empty($t['butir'])): ?>
                                    <div class="aktivitas-kosong">
                                        Pertanyaan ini belum memiliki butir lingkup pada tabel
                                        <code>mutu_lingkup</code>. Butir lingkup diatur PPM pada menu Formulir Audit.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </section>
                            <?php endforeach; ?>
                        </div>

                        <?php if (empty($topik)): ?>
                        <div class="topik-kosong">Formulir audit ini belum memiliki pertanyaan.</div>
                        <?php endif; ?>
                        <div class="topik-kosong" id="topik_cari_kosong" style="display:none;">
                            Tidak ada pertanyaan atau lingkup yang cocok dengan pencarian.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
