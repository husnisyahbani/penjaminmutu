<?php
/**
 * Detail audit (auditee) - daftar butir lingkup + jawaban auditee.
 *
 * Halaman ini hanya menampilkan:
 *   1. pertanyaan formulir (mutu_detailform) sebagai pengelompok;
 *   2. butir lingkup dari mutu_lingkup - inilah pertanyaan yang dijawab;
 *   3. kotak jawaban untuk TIAP butir lingkup (wajib, hanya saat DRAFT);
 *   4. lampiran jawaban per butir (opsional, boleh lebih dari satu berkas).
 *
 * Penilaian auditor (hasil/temuan/catatan), rencana koreksi, referensi, dan
 * daftar tilik TIDAK ditampilkan di sini - itu urutan kerja auditor dan halaman
 * PTK. Jawaban lama tingkat pertanyaan juga tidak ditampilkan lagi (datanya
 * tetap tersimpan dan masih terbaca di halaman auditor). Pertanyaan yang belum
 * punya butir lingkup tetap memakai kotak jawaban tingkat pertanyaan supaya
 * audit tidak terkunci.
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
        . 'Rencana koreksi tiap butir dapat Anda lengkapi melalui menu PTK.',
);
$pesan_kunci = isset($pesan_kunci[$status_audit])
    ? $pesan_kunci[$status_audit]
    : 'Jawaban dan lampiran hanya dapat diubah saat audit masih berstatus draft.';

/* Teks polos untuk kotak jawaban (jawaban tersimpan boleh berupa HTML
   sederhana, sedangkan kotak isian memakai teks polos). */
$teks_jawaban = function ($nilai) {
    return lingkup_bersihkan($nilai);
};

$jml_topik = count($topik);
$jml_lingkup = 0;    // butir lingkup (pertanyaan yang dijawab)
$jml_dijawab = 0;
$jml_wajib = 0;
$jml_lampiran = 0;
foreach ($topik as $t) {
    $jml_lingkup  += (int) $t['jml_butir'];
    $jml_dijawab  += (int) $t['jml_dijawab'];
    $jml_wajib    += (int) $t['jml_wajib'];
    $jml_lampiran += (int) $t['jml_lampiran'];
}
$jml_belum = $jml_wajib - $jml_dijawab;
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
                                <strong><?php echo (int) $jml_belum; ?> butir lingkup</strong> yang belum dijawab;
                                hasil evaluasi baru dapat dikirim setelah semuanya terjawab. Lampiran bersifat
                                opsional dan boleh lebih dari satu berkas.
                            </div>
                            <?php elseif (!$lampiran_siap): ?>
                            <div class="alert alert-info" role="alert">
                                Seluruh butir lingkup sudah dijawab. Fitur lampiran belum disiapkan pada database ini
                                (impor <code>database/lampiran_lingkup.sql</code>).
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="topik-berkas" id="topik_daftar" data-audit_id="<?php echo (int) $audit_id; ?>">
                            <?php foreach ($topik as $i => $t):
                                $punya_lingkup = !empty($t['punya_lingkup']);
                                $sudah = !empty($t['sudah_dijawab']);

                                /* Kata kunci pencarian: pertanyaan, butir
                                   lingkup, dan jawaban auditee. */
                                $kata = array($t['teks']);
                                if (!$punya_lingkup && isset($t['jwb']['jwb_jawaban'])) {
                                    /* Jawaban tingkat pertanyaan hanya diindeks bila memang
                                       ditampilkan, yaitu pada pertanyaan tanpa butir lingkup. */
                                    $kata[] = $teks_jawaban($t['jwb']['jwb_jawaban']);
                                }
                                foreach ($t['butir'] as $b) {
                                    $kata[] = $b['lingkup_teks'];
                                    $kata[] = $teks_jawaban($b['jwb_jawaban']);
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
                                        </span>
                                    </h4>
                                    <i class="icon md-chevron-down topik-panah" aria-hidden="true"></i>
                                </header>
                                <div class="aktivitas-daftar">

                                    <?php /* Jawaban lama tingkat pertanyaan tidak ditampilkan lagi di halaman
                                            ini: yang dijawab auditee hanya butir lingkup. Data lama tetap
                                            tersimpan di mutu_auditjawab dan masih terbaca di halaman
                                            auditor (auditor/daftaraudit/detail). */ ?>

                                    <?php $nomor = 0; ?>
                                    <?php foreach ($t['butir'] as $b):
                                        $nomor++;
                                        $jawaban = $teks_jawaban($b['jwb_jawaban']);
                                        $sudah_butir = !empty($b['sudah_dijawab']);
                                    ?>
                                    <div class="aktivitas lingkup-item"
                                         data-lingkup_id="<?php echo (int) $b['lingkup_id']; ?>"
                                         data-dtform_id="<?php echo (int) $t['dtform_id']; ?>"
                                         data-sudah-dijawab="<?php echo $sudah_butir ? '1' : '0'; ?>"
                                         data-wajib="1"
                                         data-cari="<?php echo html_escape(strtolower($b['lingkup_teks'] . ' ' . $jawaban)); ?>">
                                        <i class="icon md-comment-text aktivitas-ikon" aria-hidden="true"></i>
                                        <div class="aktivitas-isi">
                                            <span class="aktivitas-teks">
                                                <span class="lingkup-nomor"><?php echo $nomor; ?>.</span>
                                                <?php echo html_escape($b['lingkup_teks']); ?>
                                                <span class="badge lingkup-status <?php echo $sudah_butir ? 'badge-success' : 'badge-warning'; ?>">
                                                    <?php echo $sudah_butir ? 'sudah dijawab' : 'belum dijawab'; ?>
                                                </span>
                                            </span>

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

                                            <!-- Lampiran jawaban butir lingkup -->
                                            <?php if (!empty($b['lampiran']) || !$sudah_terkirim): ?>
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
