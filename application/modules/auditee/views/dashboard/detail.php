<?php
/**
 * Detail audit (auditee).
 *
 * Daftar pertanyaan yang dijawab auditee berasal dari tabel mutu_lingkup:
 * satu pertanyaan formulir (mutu_detailform) dapat memiliki banyak butir
 * lingkup, dan TIAP butir lingkup dijawab sendiri-sendiri oleh auditee
 * (tersimpan pada mutu_auditjawab dengan kolom lingkup_id terisi).
 *
 * Yang ditampilkan pada tiap butir lingkup:
 *   1. teks butir lingkup (pertanyaan yang dijawab);
 *   2. kotak jawaban auditee untuk butir itu (wajib diisi, hanya saat audit
 *      masih DRAFT);
 *   3. lampiran jawaban butir (opsional, boleh lebih dari satu berkas);
 *   4. penilaian auditor (hasil/temuan/catatan) + rencana koreksi - READ ONLY.
 *
 * Pertanyaan yang belum punya butir lingkup (data lama) tetap memakai kotak
 * jawaban tingkat pertanyaan supaya audit tidak terkunci.
 *
 * Data dari controller: $result, $audit_id, $topik, $lampiran_siap,
 * $lingkup_siap, $sudah_terkirim (jawaban dikunci bila status bukan DRAFT),
 * $status_audit.
 */
/* Pesan pengunci: jawaban & lampiran hanya dapat diubah saat audit DRAFT. */
$pesan_kunci = array(
    'TERKIRIM' => 'Hasil evaluasi sudah dikirim ke auditor, sehingga jawaban dan lampiran tidak dapat diubah lagi.',
    'PROSES'   => 'Audit sedang dinilai auditor, sehingga jawaban dan lampiran tidak dapat diubah lagi.',
    'SELESAI'  => 'Audit sudah selesai, sehingga jawaban dan lampiran tidak dapat diubah lagi. '
        . 'Rencana koreksi tiap butir dapat Anda lengkapi melalui tombol Daftar Tilik & Koreksi.',
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
$jml_lingkup = 0;    // butir lingkup (pertanyaan yang dijawab)
$jml_dijawab = 0;
$jml_wajib = 0;
$jml_temuan = 0;
$jml_koreksi = 0;
$jml_lampiran = 0;
foreach ($topik as $t) {
    $jml_lingkup  += (int) $t['jml_butir'];
    $jml_dijawab  += (int) $t['jml_dijawab'];
    $jml_wajib    += (int) $t['jml_wajib'];
    $jml_temuan   += (int) $t['jml_temuan'];
    $jml_koreksi  += (int) $t['jml_koreksi'];
    $jml_lampiran += (int) $t['jml_lampiran'];
}
$jml_belum = $jml_wajib - $jml_dijawab;

/* Teks ringkas untuk kotak jawaban (jawaban tersimpan boleh berupa HTML
   sederhana, tetapi kotak isian memakai teks polos). */
$teks_jawaban = function ($nilai) {
    return lingkup_bersihkan($nilai);
};
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
                                    <input type="text" class="form-control" id="cari_topik" placeholder="Cari pertanyaan atau butir lingkup">
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
                            <span class="badge badge-default" id="jml_lingkup"><?php echo (int) $jml_lingkup; ?> butir lingkup</span>
                            <span class="badge badge-success" id="jml_dijawab"><?php echo (int) $jml_dijawab; ?> sudah dijawab</span>
                            <span class="badge badge-warning" id="sisa_belum"><?php echo (int) $jml_belum; ?> belum dijawab</span>
                            <span class="badge badge-default" id="jml_lampiran"><?php echo (int) $jml_lampiran; ?> lampiran</span>
                            <?php if ($jml_temuan > 0): ?>
                            <span class="badge badge-warning"><?php echo (int) $jml_temuan; ?> temuan</span>
                            <?php endif; ?>
                            <?php if ($jml_koreksi > 0): ?>
                            <span class="badge badge-success"><?php echo (int) $jml_koreksi; ?> rencana koreksi</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($sudah_terkirim): ?>
                        <div class="alert alert-info" role="alert">
                            <?php echo $pesan_kunci; ?>
                        </div>
                        <?php else: ?>
                            <?php if (!$lingkup_siap): ?>
                            <div class="alert alert-warning" role="alert">
                                Tabel <code>mutu_lingkup</code> belum tersedia pada database ini, sehingga daftar
                                pertanyaan yang dijawab masih memakai pertanyaan formulir. Impor
                                <code>database/migrasi_lingkup.sql</code>, lalu jalankan menu PPM &gt; Migrasi Lingkup
                                agar tiap pertanyaan terpecah menjadi butir-butir lingkup.
                            </div>
                            <?php endif; ?>
                            <?php if ($jml_belum > 0): ?>
                            <div class="alert alert-warning" role="alert" id="peringatan_belum">
                                Setiap <strong>butir lingkup wajib dijawab</strong>. Masih ada
                                <strong><?php echo (int) $jml_belum; ?> butir lingkup</strong> yang belum dijawab; hasil
                                evaluasi baru dapat dikirim setelah semuanya terjawab. Penilaian auditor (hasil, temuan,
                                catatan) diisi auditor, auditee tidak mengisinya. Lampiran bersifat opsional dan boleh
                                lebih dari satu berkas.
                            </div>
                            <?php elseif (!$lampiran_siap): ?>
                            <div class="alert alert-info" role="alert">
                                Seluruh butir lingkup sudah dijawab. Fitur lampiran belum disiapkan pada database ini
                                (impor <code>database/lampiran_lingkup.sql</code>).
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="topik-berkas" id="topik_daftar">
                            <?php foreach ($topik as $i => $t):
                                $punya_lingkup = !empty($t['punya_lingkup']);
                                $sudah = !empty($t['sudah_dijawab']);

                                /* Kata kunci pencarian: pertanyaan, butir
                                   lingkup, jawaban, dan penilaian auditor. */
                                $kata = array($t['teks']);
                                if (isset($t['jwb']['jwb_jawaban'])) {
                                    $kata[] = $teks_jawaban($t['jwb']['jwb_jawaban']);
                                }
                                foreach ($t['butir'] as $b) {
                                    $kata[] = $b['lingkup_teks'];
                                    $kata[] = $teks_jawaban($b['jwb_jawaban']);
                                    $kata[] = (string) $b['jwb_temuan'];
                                    $kata[] = $potong($b['jwb_catatan'], 60);
                                    $kata[] = $potong($b['jwb_koreksi'], 60);
                                }
                                $cari = strtolower(implode(' ', $kata));
                            ?>
                            <section class="topik" data-dtform_id="<?php echo (int) $t['dtform_id']; ?>"
                                     data-sudah-dijawab="<?php echo $sudah ? '1' : '0'; ?>"
                                     data-punya-lingkup="<?php echo $punya_lingkup ? '1' : '0'; ?>"
                                     data-cari="<?php echo html_escape($cari); ?>">
                                <header class="topik-kepala">
                                    <span class="topik-nomor"><?php echo $i + 1; ?></span>
                                    <h4 class="topik-judul">
                                        <?php echo html_escape($t['teks']); ?>
                                        <span class="topik-info">
                                            <?php if ($punya_lingkup): ?>
                                            <span class="badge badge-info"><?php echo (int) $t['jml_butir']; ?> butir lingkup</span>
                                            <span class="badge topik-dijawab <?php echo $sudah ? 'badge-success' : 'badge-warning'; ?>">
                                                <?php echo (int) $t['jml_dijawab']; ?>/<?php echo (int) $t['jml_butir']; ?> dijawab
                                            </span>
                                            <?php else: ?>
                                            <span class="badge badge-default">tanpa butir lingkup</span>
                                            <span class="badge topik-dijawab <?php echo $sudah ? 'badge-success' : 'badge-warning'; ?>">
                                                <?php echo $sudah ? 'sudah dijawab' : 'belum dijawab'; ?>
                                            </span>
                                            <?php endif; ?>
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

                                    <?php /* Jawaban lama (tingkat pertanyaan): ditampilkan selama masih ada
                                            butir lingkup yang belum dijawab, supaya tidak hilang setelah
                                            pertanyaan dipecah menjadi butir-butir lingkup. */ ?>
                                    <?php if ($punya_lingkup && (int) $t['jml_belum'] > 0
                                            && isset($t['jwb']['jwb_jawaban'])
                                            && trim((string) $t['jwb']['jwb_jawaban']) !== ''): ?>
                                    <div class="aktivitas-bukti jawaban-lama">
                                        <span class="aktivitas-label">Jawaban lama (tingkat pertanyaan):</span>
                                        <?php echo nl2br(html_escape($teks_jawaban($t['jwb']['jwb_jawaban']))); ?>
                                        <div class="text-muted">
                                            Tersimpan sebelum pertanyaan ini dipecah menjadi butir-butir lingkup.
                                            Silakan salin ke butir lingkup yang sesuai.
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php $nomor = 0; ?>
                                    <?php foreach ($t['butir'] as $b):
                                        $bisa_dijawab = !empty($b['bisa_dijawab']) && (int) $b['lingkup_id'] > 0;
                                        if ($bisa_dijawab) {
                                            $nomor++;
                                        }
                                        $jawaban = $teks_jawaban($b['jwb_jawaban']);
                                        $sudah_butir = !empty($b['sudah_dijawab']);
                                        $hasil   = $potong($b['jwb_hasil']);
                                        $temuan  = strtoupper(trim((string) $b['jwb_temuan']));
                                        $catatan = $potong($b['jwb_catatan'], 180);
                                        $koreksi = $potong($b['jwb_koreksi'], 180);
                                        $ada_penilaian = ($hasil !== '' || $temuan !== '' || $catatan !== '');
                                        $warna = isset($warna_temuan[$temuan]) ? $warna_temuan[$temuan] : 'badge-default';
                                    ?>
                                    <div class="aktivitas lingkup-item<?php echo $bisa_dijawab ? '' : ' lingkup-item--lama'; ?>"
                                         data-lingkup_id="<?php echo (int) $b['lingkup_id']; ?>"
                                         data-dtform_id="<?php echo (int) $t['dtform_id']; ?>"
                                         data-sudah-dijawab="<?php echo $sudah_butir ? '1' : '0'; ?>"
                                         data-wajib="<?php echo $bisa_dijawab ? '1' : '0'; ?>"
                                         data-cari="<?php echo html_escape(strtolower($b['lingkup_teks'] . ' ' . $jawaban . ' ' . $temuan . ' ' . $catatan . ' ' . $koreksi)); ?>">
                                        <i class="icon <?php echo $bisa_dijawab ? 'md-comment-text' : 'md-assignment'; ?> aktivitas-ikon" aria-hidden="true"></i>
                                        <div class="aktivitas-isi">
                                            <span class="aktivitas-teks">
                                                <?php if ($bisa_dijawab): ?>
                                                <span class="lingkup-nomor"><?php echo $nomor; ?>.</span>
                                                <?php endif; ?>
                                                <?php echo html_escape($b['lingkup_teks']); ?>
                                                <?php if ($bisa_dijawab): ?>
                                                <span class="badge lingkup-status <?php echo $sudah_butir ? 'badge-success' : 'badge-warning'; ?>">
                                                    <?php echo $sudah_butir ? 'sudah dijawab' : 'belum dijawab'; ?>
                                                </span>
                                                <?php endif; ?>
                                            </span>

                                            <?php if (trim((string) $b['dtjwb_referensi']) !== ''): ?>
                                            <div class="aktivitas-bukti">
                                                <span class="aktivitas-label">Referensi:</span>
                                                <?php echo html_escape(lingkup_bersihkan($b['dtjwb_referensi'])); ?>
                                            </div>
                                            <?php endif; ?>

                                            <!-- Penilaian auditor (read-only bagi auditee) -->
                                            <div class="aktivitas-status">
                                                <?php if (!$ada_penilaian): ?>
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
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($catatan !== ''): ?>
                                            <div class="aktivitas-bukti">
                                                <span class="aktivitas-label">Catatan auditor:</span> <?php echo html_escape($catatan); ?>
                                            </div>
                                            <?php endif; ?>

                                            <?php if ($bisa_dijawab): ?>
                                            <!-- Jawaban auditee untuk butir lingkup ini -->
                                            <div class="jawaban-kotak">
                                                <?php if ($sudah_terkirim): ?>
                                                    <div class="jawaban-teks">
                                                        <?php echo $jawaban !== ''
                                                            ? nl2br(html_escape($jawaban))
                                                            : '<span class="text-muted">belum dijawab</span>'; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <label class="jawaban-label" for="jawab_<?php echo (int) $b['lingkup_id']; ?>">
                                                        Jawaban <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea class="form-control jawaban-isi" rows="2"
                                                        id="jawab_<?php echo (int) $b['lingkup_id']; ?>"
                                                        placeholder="Tulis jawaban untuk butir lingkup ini (wajib diisi)"><?php echo html_escape($jawaban); ?></textarea>
                                                    <div class="jawaban-aksi">
                                                        <button type="button" class="btn btn-sm btn-primary jawaban-simpan"
                                                                lingkup_id="<?php echo (int) $b['lingkup_id']; ?>"
                                                                dtform_id="<?php echo (int) $t['dtform_id']; ?>">
                                                            <i class="icon md-check" aria-hidden="true"></i>Simpan Jawaban
                                                        </button>
                                                        <span class="jawaban-pesan text-muted"></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php endif; ?>

                                            <!-- Lampiran butir lingkup -->
                                            <?php if (!empty($b['lampiran']) || ($bisa_dijawab && !$sudah_terkirim)): ?>
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
                                                <?php if ($bisa_dijawab && !$sudah_terkirim): ?>
                                                <div class="lampiran-unggah">
                                                    <?php if ($lampiran_siap): ?>
                                                    <input type="file" class="lampiran-berkas" name="lampiran[]" multiple>
                                                    <button type="button" class="btn btn-sm btn-primary lampiran-unggah-btn"
                                                            lingkup_id="<?php echo (int) $b['lingkup_id']; ?>"
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
                                            <?php endif; ?>

                                            <?php if ($koreksi !== ''): ?>
                                            <div class="aktivitas-bukti">
                                                <span class="aktivitas-label">Rencana koreksi:</span> <?php echo nl2br(html_escape($koreksi)); ?>
                                            </div>
                                            <?php elseif (!$bisa_dijawab && $status_audit !== 'SELESAI'): ?>
                                            <div class="aktivitas-bukti">
                                                <span class="text-muted">Rencana koreksi dapat Anda isi setelah audit selesai.</span>
                                            </div>
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

                                    <?php if (!$punya_lingkup): ?>
                                    <!-- Pertanyaan tanpa butir lingkup: jawaban tingkat pertanyaan (data lama) -->
                                    <div class="jawaban-kotak" data-wajib="1"
                                         data-sudah-dijawab="<?php echo $sudah ? '1' : '0'; ?>">
                                        <?php if ($sudah_terkirim): ?>
                                            <div class="jawaban-teks">
                                                <?php echo (isset($t['jwb']['jwb_jawaban']) && trim((string) $t['jwb']['jwb_jawaban']) !== '')
                                                    ? nl2br(html_escape($teks_jawaban($t['jwb']['jwb_jawaban'])))
                                                    : '<span class="text-muted">belum dijawab</span>'; ?>
                                            </div>
                                        <?php else: ?>
                                            <label class="jawaban-label" for="jawab_dtform_<?php echo (int) $t['dtform_id']; ?>">
                                                Jawaban pertanyaan <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control jawaban-isi" rows="2"
                                                id="jawab_dtform_<?php echo (int) $t['dtform_id']; ?>"
                                                placeholder="Tulis jawaban untuk pertanyaan ini (wajib diisi)"><?php echo html_escape($teks_jawaban(isset($t['jwb']['jwb_jawaban']) ? $t['jwb']['jwb_jawaban'] : '')); ?></textarea>
                                            <div class="jawaban-aksi">
                                                <button type="button" class="btn btn-sm btn-primary jawaban-simpan"
                                                        lingkup_id="0"
                                                        dtform_id="<?php echo (int) $t['dtform_id']; ?>">
                                                    <i class="icon md-check" aria-hidden="true"></i>Simpan Jawaban
                                                </button>
                                                <span class="jawaban-pesan text-muted"></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Lampiran lama yang menempel pada pertanyaan -->
                                    <?php if (!empty($t['lampiran'])): ?>
                                    <div class="lampiran-kotak">
                                        <span class="lampiran-judul">
                                            <i class="icon md-attachment" aria-hidden="true"></i>Lampiran pertanyaan
                                            <?php if ($punya_lingkup): ?>
                                            <span class="text-muted">(tersimpan sebelum lampiran dipindah per butir)</span>
                                            <?php endif; ?>
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
                                        <?php if (!$sudah_terkirim && !$punya_lingkup && $lampiran_siap): ?>
                                        <div class="lampiran-unggah">
                                            <input type="file" class="lampiran-berkas" name="lampiran[]" multiple>
                                            <button type="button" class="btn btn-sm btn-primary lampiran-unggah-btn"
                                                    lingkup_id="0"
                                                    dtform_id="<?php echo (int) $t['dtform_id']; ?>">
                                                <i class="icon md-cloud-upload" aria-hidden="true"></i>Unggah
                                            </button>
                                            <span class="lampiran-info text-muted">PDF, Office, gambar, atau arsip; maks 5 MB per berkas.</span>
                                        </div>
                                        <?php endif; ?>
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
                            Tidak ada pertanyaan atau butir lingkup yang cocok dengan pencarian.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
