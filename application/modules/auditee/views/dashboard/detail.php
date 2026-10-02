<?php
/**
 * Detail audit (auditee).
 *
 * Setiap pertanyaan = satu topik (gaya kursus), setiap butir lingkup wajib
 * dijawab dan boleh dilengkapi lampiran (boleh lebih dari satu berkas).
 *
 * Data dari controller: $result, $audit_id, $topik, $lampiran_siap,
 * $sudah_terkirim.
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
$jml_lingkup = 0;
$jml_dijawab = 0;
$jml_temuan = 0;
$jml_lampiran = 0;
foreach ($topik as $t) {
    $jml_lingkup  += $t['jml_butir'];
    $jml_dijawab  += $t['jml_dijawab'];
    $jml_temuan   += $t['jml_temuan'];
    $jml_lampiran += $t['jml_lampiran'];
}
$jml_belum = $jml_lingkup - $jml_dijawab;
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
                                    <a href="<?php echo base_url('auditee/dashboard'); ?>" class="btn btn-sm btn-default">
                                        <i class="icon md-undo" aria-hidden="true"></i>Kembali
                                    </a>
                                    <?php if (!$sudah_terkirim): ?>
                                    <button type="button" class="btn btn-sm btn-success" id="kirim_hasil"
                                            audit_id="<?php echo (int) $audit_id; ?>">
                                        <i class="icon md-mail-send" aria-hidden="true"></i>Kirim Hasil Evaluasi
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="panel-body">

                        <div class="topik-ringkas mb-15">
                            <span class="badge badge-info"><?php echo (int) $jml_topik; ?> pertanyaan</span>
                            <span class="badge badge-info" id="jml_lingkup"><?php echo (int) $jml_lingkup; ?> lingkup</span>
                            <span class="badge badge-success" id="jml_dijawab"><?php echo (int) $jml_dijawab; ?> sudah dijawab</span>
                            <span class="badge badge-warning" id="sisa_belum"><?php echo (int) $jml_belum; ?> belum dijawab</span>
                            <span class="badge badge-default" id="jml_lampiran"><?php echo (int) $jml_lampiran; ?> lampiran</span>
                            <?php if ($jml_temuan > 0): ?>
                            <span class="badge badge-warning"><?php echo (int) $jml_temuan; ?> temuan</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($sudah_terkirim): ?>
                        <div class="alert alert-info" role="alert">
                            Hasil evaluasi sudah dikirim ke auditor, sehingga jawaban dan lampiran tidak dapat
                            diubah lagi.
                        </div>
                        <?php elseif ($jml_belum > 0): ?>
                        <div class="alert alert-warning" role="alert" id="peringatan_belum">
                            Setiap lingkup <strong>wajib dijawab</strong>. Masih ada
                            <strong><?php echo (int) $jml_belum; ?> lingkup</strong> yang belum dijawab; hasil
                            evaluasi baru dapat dikirim setelah semuanya terjawab. Lampiran bersifat opsional dan
                            boleh lebih dari satu berkas.
                        </div>
                        <?php elseif (!$lampiran_siap): ?>
                        <div class="alert alert-info" role="alert">
                            Seluruh lingkup sudah dijawab. Fitur lampiran belum disiapkan pada database ini
                            (impor <code>database/lampiran_lingkup.sql</code>).
                        </div>
                        <?php endif; ?>

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
                                            <span class="badge badge-info"><?php echo (int) $t['jml_butir']; ?> lingkup</span>
                                            <span class="badge topik-dijawab <?php echo ($t['jml_dijawab'] >= $t['jml_butir'] && $t['jml_butir'] > 0) ? 'badge-success' : 'badge-warning'; ?>">
                                                <?php echo (int) $t['jml_dijawab']; ?>/<?php echo (int) $t['jml_butir']; ?> dijawab
                                            </span>
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
                                        <span class="aktivitas-label">Jawaban pertanyaan:</span> <?php echo html_escape($jawaban); ?>
                                    </div>
                                    <?php endif; ?>

                                    <?php foreach ($t['butir'] as $b):
                                        $terjawab = trim((string) $b['jwb_jawaban']) !== '';
                                        $hasil   = $potong($b['jwb_hasil']);
                                        $temuan  = trim((string) $b['jwb_temuan']);
                                        $catatan = $potong($b['jwb_catatan'], 180);
                                        $koreksi = $potong($b['jwb_koreksi'], 180);
                                        $ada = ($hasil !== '' || $temuan !== '' || $catatan !== '' || $koreksi !== '');
                                    ?>
                                    <div class="aktivitas" data-lingkup_id="<?php echo (int) $b['lingkup_id']; ?>"
                                         data-cari="<?php echo html_escape(strtolower($b['lingkup_teks'] . ' ' . $hasil . ' ' . $temuan . ' ' . $catatan . ' ' . $koreksi)); ?>">
                                        <i class="icon md-assignment aktivitas-ikon" aria-hidden="true"></i>
                                        <div class="aktivitas-isi">
                                            <span class="aktivitas-teks"><?php echo html_escape($b['lingkup_teks']); ?></span>

                                            <div class="aktivitas-status">
                                                <span class="badge <?php echo $terjawab ? 'badge-success' : 'badge-danger'; ?> status-jawab">
                                                    <?php echo $terjawab ? 'sudah dijawab' : 'wajib dijawab'; ?>
                                                </span>
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

                                            <div class="jawaban-kotak">
                                                <?php if ($sudah_terkirim): ?>
                                                    <div class="jawaban-teks">
                                                        <?php if ($terjawab): ?>
                                                        <?php echo html_escape(lingkup_bersihkan($b['jwb_jawaban'])); ?>
                                                        <?php else: ?>
                                                        <span class="text-muted">belum dijawab</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <label class="jawaban-label" for="jawab_<?php echo (int) $b['lingkup_id']; ?>">
                                                        Jawaban <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea class="form-control jawaban-isi" rows="2"
                                                        id="jawab_<?php echo (int) $b['lingkup_id']; ?>"
                                                        placeholder="Tulis jawaban untuk lingkup ini (wajib diisi)"><?php echo html_escape(lingkup_bersihkan($b['jwb_jawaban'])); ?></textarea>
                                                    <div class="jawaban-aksi">
                                                        <button type="button" class="btn btn-sm btn-primary jawaban-simpan">
                                                            <i class="icon md-check" aria-hidden="true"></i>Simpan Jawaban
                                                        </button>
                                                        <span class="jawaban-pesan text-muted"></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="lampiran-kotak">
                                                <span class="lampiran-judul">
                                                    <i class="icon md-attachment" aria-hidden="true"></i>Lampiran
                                                    <span class="text-muted">(opsional, boleh lebih dari satu)</span>
                                                </span>
                                                <div class="lampiran-daftar">
                                                    <?php foreach ($b['lampiran'] as $l): ?>
                                                    <div class="lampiran" data-lampiran_id="<?php echo (int) $l['lampiran_id']; ?>">
                                                        <i class="icon md-file lampiran-ikon" aria-hidden="true"></i>
                                                        <a class="lampiran-nama" target="_blank" rel="noopener"
                                                           href="<?php echo $l['url']; ?>"><?php echo html_escape($l['lampiran_asli']); ?></a>
                                                        <span class="lampiran-ukuran text-muted"><?php echo html_escape($l['ukuran_teks']); ?></span>
                                                        <?php if (!$sudah_terkirim): ?>
                                                        <button type="button" class="btn btn-sm btn-icon btn-danger lampiran-hapus"
                                                                data-info="Hapus lampiran ini" id="<?php echo (int) $l['lampiran_id']; ?>">
                                                            <i class="icon md-delete" aria-hidden="true"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <?php if (!$sudah_terkirim): ?>
                                                <div class="lampiran-unggah">
                                                    <?php if ($lampiran_siap): ?>
                                                    <input type="file" class="lampiran-berkas" name="lampiran[]" multiple>
                                                    <button type="button" class="btn btn-sm btn-primary lampiran-unggah-btn">
                                                        <i class="icon md-cloud-upload" aria-hidden="true"></i>Unggah
                                                    </button>
                                                    <span class="lampiran-info text-muted">PDF, Office, gambar, atau arsip; maks 5 MB per berkas.</span>
                                                    <?php else: ?>
                                                    <span class="text-muted">Fitur lampiran belum disiapkan (impor <code>database/lampiran_lingkup.sql</code>).</span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>

                                    <?php if (empty($t['butir'])): ?>
                                    <div class="aktivitas-kosong">Belum ada lingkup pada pertanyaan ini.</div>
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
