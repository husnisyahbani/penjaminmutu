<?php
/**
 * Detail audit (auditee) gaya halaman kursus:
 *   topik    = pertanyaan formulir (+ jawaban auditee)
 *   activity = butir lingkup + hasil/temuan auditor dan koreksi auditee
 *
 * Tombol "Jawaban & Delik" pada tiap topik membuka halaman pengisian seperti
 * semula (delik), sehingga alur menjawab tidak berubah.
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

$jml_topik = count($topik);
$jml_activity = 0;
$jml_koreksi = 0;
$jml_temuan = 0;
foreach ($topik as $t) {
    $jml_activity += $t['jml_butir'];
    $jml_koreksi  += $t['jml_koreksi'];
    $jml_temuan   += $t['jml_temuan'];
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
                                    <input type="text" class="form-control" id="cari_topik" placeholder="Cari topik atau activity">
                                </div>
                                <div class="panel-aksi__tombol">
                                    <a href="<?php echo base_url('auditee/dashboard'); ?>" class="btn btn-sm btn-default">
                                        <i class="icon md-undo" aria-hidden="true"></i>Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="panel-body">

                        <div class="topik-ringkas mb-15">
                            <span class="badge badge-info"><?php echo (int) $jml_topik; ?> topik</span>
                            <span class="badge badge-info"><?php echo (int) $jml_activity; ?> activity</span>
                            <span class="badge badge-success"><?php echo (int) $jml_koreksi; ?> sudah dikoreksi</span>
                            <span class="badge badge-warning"><?php echo (int) $jml_temuan; ?> temuan</span>
                        </div>

                        <div class="topik-berkas" id="topik_daftar">
                            <?php foreach ($topik as $i => $t):
                                $jawaban = isset($t['jwb']['jwb_jawaban']) ? $potong($t['jwb']['jwb_jawaban'], 200) : '';
                                $cari = strtolower($t['teks'] . ' ' . $jawaban . ' ' . implode(' ', array_map(function ($b) {
                                    return $b['lingkup_teks'] . ' ' . (string) $b['jwb_temuan'] . ' '
                                        . lingkup_bersihkan($b['jwb_koreksi']);
                                }, $t['butir'])));
                            ?>
                            <section class="topik" data-dtform_id="<?php echo (int) $t['dtform_id']; ?>"
                                     data-cari="<?php echo html_escape($cari); ?>">
                                <header class="topik-kepala">
                                    <span class="topik-nomor"><?php echo $i + 1; ?></span>
                                    <h4 class="topik-judul">
                                        <?php echo html_escape($t['teks']); ?>
                                        <span class="topik-info">
                                            <span class="badge badge-info"><?php echo (int) $t['jml_butir']; ?> activity</span>
                                            <?php if ($jawaban !== ''): ?>
                                            <span class="badge badge-success">sudah dijawab</span>
                                            <?php else: ?>
                                            <span class="badge badge-default">belum dijawab</span>
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
                                            <i class="icon md-edit" aria-hidden="true"></i>Jawaban &amp; Delik
                                        </button>
                                    </div>
                                </header>
                                <div class="aktivitas-daftar">
                                    <?php if ($jawaban !== ''): ?>
                                    <div class="aktivitas-bukti mb-0">
                                        <span class="aktivitas-label">Jawaban auditee:</span> <?php echo html_escape($jawaban); ?>
                                    </div>
                                    <?php endif; ?>

                                    <?php foreach ($t['butir'] as $b):
                                        $hasil   = $potong($b['jwb_hasil']);
                                        $temuan  = trim((string) $b['jwb_temuan']);
                                        $catatan = $potong($b['jwb_catatan'], 180);
                                        $koreksi = $potong($b['jwb_koreksi'], 180);
                                        $ada = ($hasil !== '' || $temuan !== '' || $catatan !== '' || $koreksi !== '');
                                    ?>
                                    <div class="aktivitas" data-cari="<?php echo html_escape(strtolower($b['lingkup_teks'] . ' ' . $hasil . ' ' . $temuan . ' ' . $catatan . ' ' . $koreksi)); ?>">
                                        <i class="icon md-assignment aktivitas-ikon" aria-hidden="true"></i>
                                        <div class="aktivitas-isi">
                                            <span class="aktivitas-teks"><?php echo html_escape($b['lingkup_teks']); ?></span>
                                            <?php if (!$ada): ?>
                                            <div class="aktivitas-status">
                                                <span class="text-muted">belum ada hasil/koreksi</span>
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
                                                <?php if ($koreksi !== ''): ?>
                                                <span class="badge badge-success">Sudah dikoreksi</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($catatan !== ''): ?>
                                            <div class="aktivitas-bukti">
                                                <span class="aktivitas-label">Catatan:</span> <?php echo html_escape($catatan); ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php if ($koreksi !== ''): ?>
                                            <div class="aktivitas-bukti">
                                                <span class="aktivitas-label">Koreksi auditee:</span> <?php echo html_escape($koreksi); ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>

                                    <?php if (empty($t['butir'])): ?>
                                    <div class="aktivitas-kosong">Belum ada activity pada topik ini.</div>
                                    <?php endif; ?>
                                </div>
                            </section>
                            <?php endforeach; ?>
                        </div>

                        <?php if (empty($topik)): ?>
                        <div class="topik-kosong">Formulir audit ini belum memiliki topik/pertanyaan.</div>
                        <?php endif; ?>
                        <div class="topik-kosong" id="topik_cari_kosong" style="display:none;">
                            Tidak ada topik atau activity yang cocok dengan pencarian.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
