<?php
/**
 * Halaman migrasi lingkup audit (menu pengelola PPM).
 *
 * Variabel:
 *   $status : ringkasan keadaan tabel
 *   $contoh : contoh hasil parse untuk beberapa pertanyaan
 */
?>
<div class="page">
    <div class="page-content container-fluid">

        <div class="alert alert-info">
            <i class="icon md-info-outline" aria-hidden="true"></i>
            Halaman ini memindahkan isi kolom lama <code>detailform.dtform_lingkup</code> (satu kolom teks)
            menjadi baris-baris tabel <code>lingkup</code>, lalu menghubungkan jawaban audit
            (<code>auditjawab</code>) ke butir lingkup tersebut. Jalankan sekali saja; aman diulang
            karena pertanyaan yang sudah punya butir akan dilewati.
            Panduan manual: <code>database/migrasi_lingkup.sql</code>.
        </div>

        <div class="row" data-by-row="true">
            <div class="col-xl-6 col-md-12">
                <div class="panel">
                    <header class="panel-heading">
                        <h3 class="panel-title">Keadaan Sekarang</h3>
                    </header>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Tabel <code>lingkup</code></td>
                                    <td><?php echo $status['tabel_lingkup'] ? '<span class="badge badge-success">sudah ada</span>' : '<span class="badge badge-default">belum ada</span>'; ?></td>
                                </tr>
                                <tr>
                                    <td>Kolom <code>auditjawab.lingkup_id</code></td>
                                    <td><?php echo $status['kolom_jawab'] ? '<span class="badge badge-success">sudah ada</span>' : '<span class="badge badge-default">belum ada</span>'; ?></td>
                                </tr>
                                <tr>
                                    <td>Kolom lama <code>detailform.dtform_lingkup</code></td>
                                    <td><?php echo $status['kolom_lama'] ? '<span class="badge badge-warning">masih ada</span>' : '<span class="badge badge-default">sudah dihapus</span>'; ?></td>
                                </tr>
                                <tr>
                                    <td>Jumlah pertanyaan (detailform)</td>
                                    <td><strong><?php echo (int) $status['jml_dtform']; ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Butir lingkup tersimpan</td>
                                    <td><strong><?php echo (int) $status['jml_butir']; ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Jawaban audit berelasi butir</td>
                                    <td><strong><?php echo (int) $status['jml_jawaban_butir']; ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Jawaban audit lama (tanpa butir)</td>
                                    <td><strong><?php echo (int) $status['jml_jawaban_lama']; ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Kolom jawaban per butir <code>auditjawabdetail.dtjwb_jawaban</code></td>
                                    <td><?php echo $status['jawaban_tilik'] ? '<span class="badge badge-success">sudah ada</span>' : '<span class="badge badge-default">belum ada</span>'; ?></td>
                                </tr>
                                <tr>
                                    <td>Kolom koreksi per butir <code>auditjawabdetail.dtjwb_koreksi</code></td>
                                    <td><?php echo $status['koreksi_butir'] ? '<span class="badge badge-success">sudah ada</span>' : '<span class="badge badge-default">belum ada</span>'; ?></td>
                                </tr>
                                <tr>
                                    <td>Baris tilik lama (<code>auditjawabdetail</code>)</td>
                                    <td><strong><?php echo (int) $status['jml_tilik_lama']; ?></strong>
                                        (<?php echo (int) $status['jml_tilik_belum']; ?> belum dipindahkan)</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="text-right">
                            <button type="button" class="btn btn-primary" id="jalankan">
                                <i class="icon md-refresh" aria-hidden="true"></i>Jalankan Migrasi
                            </button>
                            <button type="button" class="btn btn-info" id="siapkankoreksi" <?php echo ($status['koreksi_butir'] && $status['jawaban_tilik']) ? 'disabled' : ''; ?>>
                                <i class="icon md-plus" aria-hidden="true"></i>Siapkan Kolom Tilik (jawaban &amp; koreksi)
                            </button>
                            <button type="button" class="btn btn-danger" id="hapuskolom" <?php echo $status['kolom_lama'] ? '' : 'disabled'; ?>>
                                <i class="icon md-delete" aria-hidden="true"></i>Hapus Kolom Lama
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-12">
                <div class="panel">
                    <header class="panel-heading">
                        <h3 class="panel-title">Contoh Hasil Parse</h3>
                    </header>
                    <div class="panel-body">
                        <?php if (empty($contoh)): ?>
                            <p class="text-muted">Tidak ada kolom lama yang perlu di-parse.</p>
                        <?php else: ?>
                            <?php foreach ($contoh as $c): ?>
                                <div class="migrasi-contoh">
                                    <div class="font-weight-600">
                                        #<?php echo (int) $c['dtform_id']; ?>
                                        <span class="badge badge-info"><?php echo count($c['butir']); ?> butir</span>
                                        <?php if ($c['jml_tersimpan']): ?>
                                            <span class="badge badge-success">sudah tersimpan</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-muted mb-5"><?php echo html_escape($c['pertanyaan']); ?></p>
                                    <ol class="lingkup-daftar">
                                        <?php foreach ($c['butir'] as $b): ?>
                                            <li><?php echo html_escape($b); ?></li>
                                        <?php endforeach; ?>
                                    </ol>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .migrasi-contoh { border-bottom: 1px solid #ececec; padding: 10px 0; }
    .migrasi-contoh:last-child { border-bottom: 0; }
    .lingkup-daftar { margin: 0 0 0 18px; padding: 0; }
    .lingkup-daftar li { margin-bottom: 3px; }
    .lingkup-ringkas { margin: 3px 0 0 16px; padding: 0; font-size: 12px; }
    .lingkup-ringkas li { margin-bottom: 2px; }
</style>
