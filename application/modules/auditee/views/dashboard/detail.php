<?php
/**
 * Detail audit (auditee).
 *
 * Satu pertanyaan = satu topik. Yang ditampilkan pada tiap topik:
 *   1. penilaian auditor (butir tilik dari mutu_auditjawabdetail) - READ ONLY,
 *      auditee tidak menjawabnya;
 *   2. kotak jawaban auditee untuk PERTANYAAN itu (wajib diisi, hanya saat
 *      audit masih DRAFT);
 *   3. lampiran jawaban pertanyaan (opsional, boleh lebih dari satu berkas).
 *
 * Data dari controller: $result, $audit_id, $topik, $lampiran_siap,
 * $sudah_terkirim (jawaban dikunci bila status bukan DRAFT), $status_audit.
 */
/* Pesan pengunci: jawaban & lampiran hanya dapat diubah saat audit DRAFT. */
$pesan_kunci = array(
    'TERKIRIM' => 'Hasil evaluasi sudah dikirim ke auditor, sehingga jawaban dan lampiran tidak dapat diubah lagi.',
    'PROSES'   => 'Audit sedang dinilai auditor, sehingga jawaban dan lampiran tidak dapat diubah lagi.',
    'SELESAI'  => 'Audit sudah selesai, sehingga jawaban dan lampiran tidak dapat diubah lagi.',
);
$pesan_kunci = isset($pesan_kunci[$status_audit])
    ? $pesan_kunci[$status_audit]
    : 'Jawaban dan lampiran hanya dapat diubah saat audit masih berstatus draft.';

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

/* Warna badge mengikuti jenis penilaian auditor. */
$warna_temuan = array(
    'S'        => 'badge-success',
    'OB'       => 'badge-info',
    'TS MINOR' => 'badge-warning',
    'TS MAYOR' => 'badge-danger',
);

$jml_topik = count($topik);
$jml_butir = 0;
$jml_dijawab = 0;
$jml_temuan = 0;
$jml_lampiran = 0;
foreach ($topik as $t) {
    $jml_butir    += $t['jml_butir'];
    $jml_dijawab  += !empty($t['sudah_dijawab']) ? 1 : 0;
    $jml_temuan   += $t['jml_temuan'];
    $jml_lampiran += $t['jml_lampiran'];
}
$jml_belum = $jml_topik - $jml_dijawab;
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
                                    <input type="text" class="form-control" id="cari_topik" placeholder="Cari pertanyaan atau butir tilik">
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
                            <span class="badge badge-default" id="jml_lingkup"><?php echo (int) $jml_butir; ?> butir tilik</span>
                            <span class="badge badge-success" id="jml_dijawab"><?php echo (int) $jml_dijawab; ?> sudah dijawab</span>
                            <span class="badge badge-warning" id="sisa_belum"><?php echo (int) $jml_belum; ?> belum dijawab</span>
                            <span class="badge badge-default" id="jml_lampiran"><?php echo (int) $jml_lampiran; ?> lampiran</span>
                            <?php if ($jml_temuan > 0): ?>
                            <span class="badge badge-warning"><?php echo (int) $jml_temuan; ?> temuan</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($sudah_terkirim): ?>
                        <div class="alert alert-info" role="alert">
                            <?php echo $pesan_kunci; ?>
                        </div>
                        <?php elseif ($jml_belum > 0): ?>
                        <div class="alert alert-warning" role="alert" id="peringatan_belum">
                            Setiap pertanyaan <strong>wajib dijawab</strong>. Masih ada
                            <strong><?php echo (int) $jml_belum; ?> pertanyaan</strong> yang belum dijawab; hasil
                            evaluasi baru dapat dikirim setelah semuanya terjawab. Butir tilik diisi auditor,
                            auditee tidak menjawabnya. Lampiran bersifat opsional dan boleh lebih dari satu berkas.
                        </div>
                        <?php elseif (!$lampiran_siap): ?>
                        <div class="alert alert-info" role="alert">
                            Seluruh pertanyaan sudah dijawab. Fitur lampiran belum disiapkan pada database ini
                            (impor <code>database/lampiran_lingkup.sql</code>).
                        </div>
                        <?php endif; ?>

                        <div class="topik-berkas" id="topik_daftar">
                            <?php foreach ($topik as $i => $t):
                                $jawaban = isset($t['jwb']['jwb_jawaban']) ? $potong($t['jwb']['jwb_jawaban'], 200) : '';
                                $cari = strtolower($t['teks'] . ' ' . $jawaban . ' ' . implode(' ', array_map(function ($b) use ($potong) {
                                    return $b['lingkup_teks'] . ' ' . (string) $b['jwb_temuan'] . ' '
                                        . $potong($b['jwb_catatan'], 60) . ' ' . $potong($b['jwb_koreksi'], 60);
                                }, $t['butir'])));
                                $sudah = !empty($t['sudah_dijawab']);
                            ?>
                            <section class="topik" data-dtform_id="<?php echo (int) $t['dtform_id']; ?>"
                                     data-sudah-dijawab="<?php echo $sudah ? '1' : '0'; ?>"
                                     data-cari="<?php echo html_escape($cari); ?>">
                                <header class="topik-kepala">
                                    <span class="topik-nomor"><?php echo $i + 1; ?></span>
                                    <h4 class="topik-judul">
                                        <?php echo html_escape($t['teks']); ?>
                                        <span class="topik-info">
                                            <span class="badge badge-info"><?php echo (int) $t['jml_butir']; ?> butir tilik</span>
                                            <span class="badge topik-dijawab <?php echo $sudah ? 'badge-success' : 'badge-warning'; ?>">
                                                <?php echo $sudah ? 'sudah dijawab' : 'belum dijawab'; ?>
                                            </span>
                                            <?php if ($t['jml_temuan'] > 0): ?>
                                            <span class="badge badge-warning"><?php echo (int) $t['jml_temuan']; ?> temuan</span>
                                            <?php endif; ?>
                                            <?php if (!empty($t['jml_koreksi'])): ?>
                                            <span class="badge badge-success"><?php echo (int) $t['jml_koreksi']; ?> rencana koreksi</span>
                                            <?php endif; ?>
                                        </span>
                                    </h4>
                                    <i class="icon md-chevron-down topik-panah" aria-hidden="true"></i>
                                    <div class="topik-aksi">
                                        <button type="button" class="delik btn btn-sm btn-primary"
                                                dtform_id="<?php echo (int) $t['dtform_id']; ?>"
                                                audit_id="<?php echo (int) $audit_id; ?>">
                                            <i class="icon md-edit" aria-hidden="true"></i>Daftar Tilik &amp; Koreksi
                                        </button>
                                    </div>
                                </header>
                                <div class="aktivitas-daftar">

                                    <?php if (isset($t['jwb']['jwb_tujuan']) && trim((string) $t['jwb']['jwb_tujuan']) !== ''): ?>
                                    <div class="aktivitas-bukti">
                                        <span class="aktivitas-label">Tujuan:</span>
                                        <?php echo nl2br(html_escape(lingkup_bersihkan($t['jwb']['jwb_tujuan']))); ?>
                                    </div>
                                    <?php endif; ?>

                                    <?php foreach ($t['butir'] as $b):
                                        $hasil   = $potong($b['jwb_hasil']);
                                        $temuan  = strtoupper(trim((string) $b['jwb_temuan']));
                                        $catatan = $potong($b['jwb_catatan'], 180);
                                        $koreksi = $potong($b['jwb_koreksi'], 180);
                                        $ada = ($hasil !== '' || $temuan !== '' || $catatan !== '' || $koreksi !== '');
                                        $warna = isset($warna_temuan[$temuan]) ? $warna_temuan[$temuan] : 'badge-default';
                                    ?>
                                    <div class="aktivitas" data-cari="<?php echo html_escape(strtolower($b['lingkup_teks'] . ' ' . $temuan . ' ' . $catatan . ' ' . $koreksi)); ?>">
                                        <i class="icon md-assignment aktivitas-ikon" aria-hidden="true"></i>
                                        <div class="aktivitas-isi">
                                            <span class="aktivitas-teks"><?php echo html_escape($b['lingkup_teks']); ?></span>
                                            <?php if (trim((string) $b['dtjwb_referensi']) !== ''): ?>
                                            <div class="aktivitas-bukti">
                                                <span class="aktivitas-label">Referensi:</span>
                                                <?php echo html_escape(lingkup_bersihkan($b['dtjwb_referensi'])); ?>
                                            </div>
                                            <?php endif; ?>

                                            <div class="aktivitas-status">
                                                <?php if (!$ada): ?>
                                                <span class="text-muted">belum dinilai auditor</span>
                                                <?php else: ?>
                                                <?php if ($temuan !== ''): ?>
                                                <span class="badge <?php echo $warna; ?>"><?php echo html_escape($temuan); ?></span>
                                                <?php endif; ?>
                                                <?php if ($hasil !== ''): ?>
                                                <span class="badge badge-primary">Hasil: <?php echo html_escape($hasil); ?></span>
                                                <?php endif; ?>
                                                <?php if ($catatan !== ''): ?>
                                                <span class="badge badge-info">Catatan</span>
                                                <?php endif; ?>
                                                <?php if ($koreksi !== ''): ?>
                                                <span class="badge badge-success">Ada rencana koreksi</span>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($catatan !== ''): ?>
                                            <div class="aktivitas-bukti">
                                                <span class="aktivitas-label">Catatan auditor:</span> <?php echo html_escape($catatan); ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php if ($koreksi !== ''): ?>
                                            <div class="aktivitas-bukti">
                                                <span class="aktivitas-label">Rencana koreksi:</span> <?php echo nl2br(html_escape($koreksi)); ?>
                                            </div>
                                            <?php endif; ?>

                                            <?php foreach ($b['lampiran'] as $l): ?>
                                            <div class="lampiran-daftar">
                                                <div class="lampiran" data-lampiran_id="<?php echo (int) $l['lampiran_id']; ?>">
                                                    <i class="icon md-file lampiran-ikon" aria-hidden="true"></i>
                                                    <a class="lampiran-nama" target="_blank" rel="noopener"
                                                       href="<?php echo $l['url']; ?>"><?php echo html_escape($l['lampiran_asli']); ?></a>
                                                    <span class="lampiran-ukuran text-muted"><?php echo html_escape($l['ukuran_teks']); ?></span>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>

                                    <?php if (empty($t['butir'])): ?>
                                    <div class="aktivitas-kosong">Belum ada butir tilik pada pertanyaan ini (diisi auditor melalui halaman Daftar Tilik).</div>
                                    <?php endif; ?>

                                    <!-- Jawaban auditee untuk pertanyaan ini -->
                                    <div class="jawaban-kotak">
                                        <?php if ($sudah_terkirim): ?>
                                            <div class="jawaban-teks">
                                                <?php if ($jawaban !== ''): ?>
                                                <?php echo html_escape($jawaban); ?>
                                                <?php else: ?>
                                                <span class="text-muted">belum dijawab</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <label class="jawaban-label" for="jawab_<?php echo (int) $t['dtform_id']; ?>">
                                                Jawaban pertanyaan <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control jawaban-isi" rows="2"
                                                id="jawab_<?php echo (int) $t['dtform_id']; ?>"
                                                placeholder="Tulis jawaban untuk pertanyaan ini (wajib diisi)"><?php echo html_escape(lingkup_bersihkan(isset($t['jwb']['jwb_jawaban']) ? $t['jwb']['jwb_jawaban'] : '')); ?></textarea>
                                            <div class="jawaban-aksi">
                                                <button type="button" class="btn btn-sm btn-primary jawaban-simpan"
                                                        dtform_id="<?php echo (int) $t['dtform_id']; ?>">
                                                    <i class="icon md-check" aria-hidden="true"></i>Simpan Jawaban
                                                </button>
                                                <span class="jawaban-pesan text-muted"></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Lampiran jawaban pertanyaan -->
                                    <div class="lampiran-kotak">
                                        <span class="lampiran-judul">
                                            <i class="icon md-attachment" aria-hidden="true"></i>Lampiran
                                            <span class="text-muted">(opsional, boleh lebih dari satu)</span>
                                        </span>
                                        <div class="lampiran-daftar">
                                            <?php foreach ($t['lampiran'] as $l): ?>
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
                                            <button type="button" class="btn btn-sm btn-primary lampiran-unggah-btn"
                                                    dtform_id="<?php echo (int) $t['dtform_id']; ?>">
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
                            </section>
                            <?php endforeach; ?>
                        </div>

                        <?php if (empty($topik)): ?>
                        <div class="topik-kosong">Formulir audit ini belum memiliki pertanyaan.</div>
                        <?php endif; ?>
                        <div class="topik-kosong" id="topik_cari_kosong" style="display:none;">
                            Tidak ada pertanyaan atau butir tilik yang cocok dengan pencarian.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
