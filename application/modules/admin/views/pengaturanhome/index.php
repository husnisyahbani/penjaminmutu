<?php
/**
 * Pengaturan Home - panel admin
 *
 * Variabel:
 *   $tabs            : susunan tab (pengaturan + item)
 *   $grup_pengaturan : pengaturan dikelompokkan per grup
 *   $grup_item       : blueprint item (judul, keterangan, fields)
 *   $items           : item per grup
 *   $terpasang       : apakah tabel pengaturan sudah dibuat
 */

/** Render satu baris input pengaturan sesuai tipenya. */
if (!function_exists('ph_input')) {
    function ph_input($row)
    {
        $kunci = $row['kunci'];
        $nilai = $row['nilai'];
        $nama  = 'setting[' . $kunci . ']';

        switch ($row['tipe']) {

            case 'textarea':
                return '<textarea class="form-control" name="' . $nama . '" rows="5">' . htmlspecialchars($nilai, ENT_QUOTES) . '</textarea>';

            case 'color':
                return '<div class="input-group" style="max-width:220px;">'
                    . '<input type="color" class="form-control" style="height:38px;padding:3px;" name="' . $nama . '" value="' . htmlspecialchars($nilai !== '' ? $nilai : '#0f766e', ENT_QUOTES) . '">'
                    . '<span class="input-group-text">' . htmlspecialchars($nilai) . '</span>'
                    . '</div>';

            case 'image':
                $tampil = $nilai !== '' && !preg_match('#^(https?:)?//#i', $nilai) ? base_url($nilai) : $nilai;
                $html = '<div class="d-flex flex-wrap align-items-center" style="gap:14px;">';
                $html .= '<span class="d-inline-flex align-items-center justify-content-center" style="width:110px;height:80px;border:1px dashed #cfd8e3;border-radius:8px;background:#f8fafc;overflow:hidden;">';
                $html .= ($tampil !== '')
                    ? '<img src="' . htmlspecialchars($tampil) . '" alt="preview" style="max-width:100%;max-height:76px;">'
                    : '<i class="icon md-image" aria-hidden="true" style="font-size:22px;color:#9aa9bd;"></i>';
                $html .= '</span>';
                $html .= '<div style="min-width:240px;">';
                $html .= '<input type="file" class="form-control" name="file_' . $kunci . '" accept="image/*">';
                $html .= '<input type="hidden" name="' . $nama . '" value="' . htmlspecialchars($nilai, ENT_QUOTES) . '">';
                $html .= '<small class="text-muted d-block mt-1">Kosongkan bila tidak ingin mengganti gambar.</small>';
                $html .= '</div>';
                $html .= '</div>';
                return $html;

            case 'toggle':
                $aktif = ($nilai === '1' || $nilai === 1 || $nilai === TRUE);
                return '<input type="hidden" name="' . $nama . '" value="0">'
                    . '<div class="checkbox-custom checkbox-inline checkbox-primary">'
                    . '<input type="checkbox" id="cek_' . $kunci . '" data-plugin="switchery" name="' . $nama . '" value="1" ' . ($aktif ? 'checked' : '') . '>'
                    . '<label for="cek_' . $kunci . '">Tampilkan</label>'
                    . '</div>';

            default:
                return '<input type="text" class="form-control" name="' . $nama . '" value="' . htmlspecialchars($nilai, ENT_QUOTES) . '">';
        }
    }
}

/** Potong teks panjang untuk kolom tabel (aman tanpa ekstensi mbstring). */
if (!function_exists('ph_potong')) {
    function ph_potong($teks, $panjang = 120)
    {
        $teks = trim(preg_replace('/\s+/', ' ', strip_tags((string) $teks)));

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($teks) > $panjang ? mb_substr($teks, 0, $panjang) . ' ...' : $teks;
        }

        return strlen($teks) > $panjang ? substr($teks, 0, $panjang) . ' ...' : $teks;
    }
}

?>

<!-- Page -->
<div class="page">

  <div class="page-content container-fluid">

    <div class="panel">

      <header class="panel-heading">
        <h3 class="panel-title">
          Pengaturan Home <span class="text-muted">/ <?php echo htmlspecialchars($tab_aktif['label']); ?></span>
        </h3>
        <div class="panel-actions panel-actions-keep">
          <a class="btn btn-sm btn-icon btn-outline btn-default" href="<?php echo base_url(); ?>" target="_blank" rel="noopener">
            <i class="icon md-open-in-new" aria-hidden="true"></i> Lihat Halaman
          </a>
          <?php if (!$terpasang): ?>
            <button type="button" class="btn btn-sm btn-icon btn-warning" id="pasangTabel">
              <i class="icon md-storage" aria-hidden="true"></i> Buat Tabel Pengaturan
            </button>
          <?php endif; ?>
        </div>
      </header>

      <div class="panel-body">

        <p class="text-muted mb-4">
          Semua teks, gambar, warna, dan section pada halaman depan dapat diubah dari halaman ini.
          Perubahan langsung tampil pada <a href="<?php echo base_url(); ?>" target="_blank" rel="noopener">halaman depan</a>
          setelah tombol <strong>Simpan</strong> pada tiap tab ditekan.
        </p>

        <?php if (!$terpasang): ?>
          <div class="alert alert-warning" role="alert" id="peringatanTabel">
            <h4 class="alert-heading mb-2">Tabel pengaturan belum dibuat</h4>
            <p class="mb-2">
              Halaman depan sementara memakai konten bawaan. Klik tombol
              <strong>Buat Tabel Pengaturan</strong> di atas untuk menyiapkan tabel
              <code>mutu_home_setting</code> dan <code>mutu_home_item</code> beserta isi bawaannya.
            </p>
            <p class="mb-0">
              Alternatif lain: impor berkas <code>database/pengaturan_home.sql</code> melalui phpMyAdmin.
            </p>
          </div>
        <?php endif; ?>

        <!-- Sub menu: setiap bagian punya URL sendiri -->
        <ul class="nav nav-tabs nav-tabs-line" role="tablist">
          <?php foreach ($tabs as $tab): ?>
            <li class="nav-item" role="presentation">
              <a class="nav-link <?php echo ($tab['id'] === $tab_aktif['id']) ? 'active' : ''; ?>"
                 href="<?php echo base_url($module . '/pengaturanhome/' . $tab['id']); ?>">
                <i class="icon <?php echo $tab['ikon']; ?>" aria-hidden="true"></i>
                <?php echo htmlspecialchars($tab['label']); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="tab-content pt-20">

          <?php foreach ($tabs as $tab): ?>
            <?php if ($tab['id'] !== $tab_aktif['id']) { continue; } // hanya bagian aktif ?>
            <div class="tab-pane active"
                 id="tab-<?php echo $tab['id']; ?>"
                 role="tabpanel">

              <!-- ============ PENGATURAN ============ -->
              <?php foreach ($tab['pengaturan'] as $grup): ?>
                <?php if (!isset($grup_pengaturan[$grup])) { continue; } ?>
                <?php $bagian = $grup_pengaturan[$grup]; ?>

                <?php if (empty($bagian['rows'])) { continue; } ?>

                <form class="form-pengaturan" data-grup="<?php echo $grup; ?>" data-judul="<?php echo htmlspecialchars($bagian['judul'], ENT_QUOTES); ?>" style="max-width:820px;">
                  <div class="mb-20">
                    <h4 class="mb-1">
                      <?php if (!empty($bagian['ikon'])): ?><i class="icon <?php echo $bagian['ikon']; ?>" aria-hidden="true"></i> <?php endif; ?>
                      <?php echo htmlspecialchars($bagian['judul']); ?>
                    </h4>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($bagian['keterangan']); ?></p>
                  </div>

                  <?php foreach ($bagian['rows'] as $row): ?>
                    <div class="form-group">
                      <label class="form-control-label"><?php echo htmlspecialchars($row['label']); ?></label>
                      <?php echo ph_input($row); ?>
                      <?php if (!empty($row['bantuan'])): ?>
                        <small class="text-muted d-block mt-1"><?php echo htmlspecialchars($row['bantuan']); ?></small>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>

                  <div class="form-group mb-30">
                    <button type="submit" class="btn btn-primary">
                      <i class="icon md-check" aria-hidden="true"></i> Simpan <?php echo htmlspecialchars($bagian['judul']); ?>
                    </button>
                  </div>
                </form>
              <?php endforeach; ?>

              <!-- ============ ITEM ============ -->
              <?php foreach ($tab['item'] as $grup): ?>
                <?php if (!isset($grup_item[$grup])) { continue; } ?>
                <?php $bagian = $grup_item[$grup]; ?>
                <?php $daftar = isset($items[$grup]) ? $items[$grup] : array(); ?>
                <?php $fields = $bagian['fields']; ?>

                <div class="mb-20 d-flex flex-wrap justify-content-between align-items-center" style="gap:12px;">
                  <div>
                    <h4 class="mb-1">
                      <?php if (!empty($bagian['ikon'])): ?><i class="icon <?php echo $bagian['ikon']; ?>" aria-hidden="true"></i> <?php endif; ?>
                      <?php echo htmlspecialchars($bagian['judul']); ?>
                    </h4>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($bagian['keterangan']); ?></p>
                  </div>
                  <button type="button"
                          class="btn btn-success btn-sm tambah-item"
                          data-grup="<?php echo htmlspecialchars($grup, ENT_QUOTES); ?>">
                    <i class="icon md-plus" aria-hidden="true"></i> Tambah
                  </button>
                </div>

                <div class="table-responsive">
                  <table class="table table-striped table-hover w-full">
                    <thead>
                      <tr>
                        <th width="60">No</th>
                        <?php if (in_array('judul', $fields, TRUE)): ?><th>Judul</th><?php endif; ?>
                        <?php if (in_array('isi', $fields, TRUE)): ?><th>Isi</th><?php endif; ?>
                        <?php if (in_array('gambar', $fields, TRUE)): ?><th width="110">Gambar</th><?php endif; ?>
                        <?php if (in_array('warna', $fields, TRUE)): ?><th width="80">Warna</th><?php endif; ?>
                        <?php if (in_array('link', $fields, TRUE)): ?><th width="150">Link</th><?php endif; ?>
                        <th width="80">Urutan</th>
                        <th width="110">Status</th>
                        <th width="150">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($daftar)): ?>
                        <tr>
                          <td colspan="9" class="text-center text-muted py-4">
                            Belum ada data. Klik <strong>Tambah</strong> untuk membuat konten baru.
                          </td>
                        </tr>
                      <?php else: ?>
                        <?php $no = 1; foreach ($daftar as $item): ?>
                          <tr>
                            <td><?php echo $no++; ?></td>
                            <?php if (in_array('judul', $fields, TRUE)): ?>
                              <td><?php echo htmlspecialchars($item['item_judul']); ?></td>
                            <?php endif; ?>
                            <?php if (in_array('isi', $fields, TRUE)): ?>
                              <td><?php echo htmlspecialchars(ph_potong($item['item_isi'])); ?></td>
                            <?php endif; ?>
                            <?php if (in_array('gambar', $fields, TRUE)): ?>
                              <td>
                                <?php if (!empty($item['item_gambar'])): ?>
                                  <img src="<?php echo htmlspecialchars($item['item_gambar'] !== '' && !preg_match('#^(https?:)?//#i', $item['item_gambar']) ? base_url($item['item_gambar']) : $item['item_gambar'], ENT_QUOTES); ?>"
                                       alt="gambar" style="width:70px;height:48px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                  <span class="text-muted">-</span>
                                <?php endif; ?>
                              </td>
                            <?php endif; ?>
                            <?php if (in_array('warna', $fields, TRUE)): ?>
                              <td>
                                <?php if (!empty($item['item_warna']) && preg_match('/^#[0-9a-f]{6}$/i', $item['item_warna'])): ?>
                                  <span style="display:inline-block;width:26px;height:26px;border-radius:8px;border:1px solid #dbe2ea;background:<?php echo htmlspecialchars($item['item_warna'], ENT_QUOTES); ?>;"></span>
                                <?php else: ?>
                                  <span class="text-muted">-</span>
                                <?php endif; ?>
                              </td>
                            <?php endif; ?>
                            <?php if (in_array('link', $fields, TRUE)): ?>
                              <td><small class="text-muted"><?php echo htmlspecialchars((string) $item['item_link']); ?></small></td>
                            <?php endif; ?>
                            <td><?php echo (int) $item['item_urutan']; ?></td>
                            <td>
                              <?php if ((int) $item['item_status'] === 1): ?>
                                <span class="badge badge-success">Tampil</span>
                              <?php else: ?>
                                <span class="badge badge-default">Disembunyikan</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <button type="button" class="btn btn-sm btn-icon btn-pure btn-default edit-item"
                                      data-id="<?php echo (int) $item['item_id']; ?>" title="Edit">
                                <i class="icon md-edit" aria-hidden="true"></i>
                              </button>
                              <button type="button" class="btn btn-sm btn-icon btn-pure btn-default status-item"
                                      data-id="<?php echo (int) $item['item_id']; ?>" title="Tampil / Sembunyikan">
                                <i class="icon md-eye" aria-hidden="true"></i>
                              </button>
                              <button type="button" class="btn btn-sm btn-icon btn-pure btn-default hapus-item"
                                      data-id="<?php echo (int) $item['item_id']; ?>" title="Hapus">
                                <i class="icon md-delete" aria-hidden="true"></i>
                              </button>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              <?php endforeach; ?>

              <!-- navigasi antar bagian -->
              <hr class="mt-30">
              <div class="d-flex flex-wrap justify-content-between align-items-center" style="gap:12px;">
                <div>
                  <?php if ($tab_sebelum): ?>
                    <a class="btn btn-outline btn-default"
                       href="<?php echo base_url($module . '/pengaturanhome/' . $tab_sebelum['id']); ?>">
                      <i class="icon md-chevron-left" aria-hidden="true"></i> <?php echo htmlspecialchars($tab_sebelum['label']); ?>
                    </a>
                  <?php endif; ?>
                </div>
                <div>
                  <?php if ($tab_berikut): ?>
                    <a class="btn btn-outline btn-primary"
                       href="<?php echo base_url($module . '/pengaturanhome/' . $tab_berikut['id']); ?>">
                      <?php echo htmlspecialchars($tab_berikut['label']); ?> <i class="icon md-chevron-right" aria-hidden="true"></i>
                    </a>
                  <?php endif; ?>
                </div>
              </div>

            </div>
          <?php endforeach; ?>

        </div>

      </div>
    </div>

  </div>
</div>
<!-- End Page -->

<!-- ================= MODAL ITEM ================= -->
<div class="modal fade" id="itemModal" aria-hidden="true" role="dialog" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="formitem" class="modal-content" method="post" enctype="multipart/form-data">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="itemModalTitle">Tambah Konten</h4>
      </div>

      <div class="modal-body">
        <input type="hidden" name="item_id" id="item_id">
        <input type="hidden" name="item_grup" id="item_grup">

        <div class="form-group" data-field="judul">
          <label class="form-control-label">Judul</label>
          <input type="text" class="form-control" name="item_judul" id="item_judul" placeholder="Masukkan judul">
        </div>

        <div class="form-group" data-field="isi">
          <label class="form-control-label">Isi / Deskripsi</label>
          <textarea class="form-control" name="item_isi" id="item_isi" rows="4" placeholder="Masukkan isi"></textarea>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group" data-field="ikon">
              <label class="form-control-label">Ikon</label>
              <input type="text" class="form-control" name="item_ikon" id="item_ikon" placeholder="bi-shield-check">
              <small class="text-muted d-block mt-1">
                Nama ikon Bootstrap Icons, contoh: <code>bi-shield-check</code>, <code>bi-people</code>, <code>bi-clipboard-check</code>.
              </small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group" data-field="warna">
              <label class="form-control-label">Warna</label>
              <input type="color" class="form-control" style="height:38px;padding:3px;max-width:140px;" name="item_warna" id="item_warna" value="#0f766e">
              <small class="text-muted d-block mt-1">Warna aksen kartu/ikon.</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group" data-field="label_link">
              <label class="form-control-label">Label Tombol</label>
              <input type="text" class="form-control" name="item_label_link" id="item_label_link" placeholder="Login sebagai PPM">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group" data-field="link">
              <label class="form-control-label">Link</label>
              <input type="text" class="form-control" name="item_link" id="item_link" placeholder="login / https://...">
              <small class="text-muted d-block mt-1">Boleh berupa alamat halaman (mis. <code>login</code>) atau URL lengkap.</small>
            </div>
          </div>
        </div>

        <div class="form-group" data-field="gambar">
          <label class="form-control-label">Gambar</label>
          <div class="d-flex flex-wrap align-items-center" style="gap:14px;">
            <span class="d-inline-flex align-items-center justify-content-center" style="width:130px;height:90px;border:1px dashed #cfd8e3;border-radius:8px;background:#f8fafc;overflow:hidden;">
              <img id="item_gambar_preview" src="" alt="preview" style="max-width:100%;max-height:86px;display:none;">
              <i class="icon md-image" id="item_gambar_kosong" aria-hidden="true" style="font-size:22px;color:#9aa9bd;"></i>
            </span>
            <div style="min-width:260px;">
              <input type="file" class="form-control" name="item_gambar" id="item_gambar" accept="image/*">
              <input type="hidden" name="item_gambar_lama" id="item_gambar_lama">
              <label class="checkbox-custom checkbox-inline mt-2" data-field="gambar">
                <input type="checkbox" name="hapus_gambar" id="hapus_gambar" value="1">
                <span>Hapus gambar saat ini</span>
              </label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group" data-field="urutan">
              <label class="form-control-label">Urutan</label>
              <input type="number" class="form-control" name="item_urutan" id="item_urutan" value="0" min="0">
              <small class="text-muted d-block mt-1">Angka kecil tampil lebih dahulu.</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group" data-field="status">
              <label class="form-control-label">Status</label>
              <select class="form-control" name="item_status" id="item_status">
                <option value="1">Tampilkan</option>
                <option value="0">Sembunyikan</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="simpanItem">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script type="text/javascript">
  // konfigurasi field tiap grup konten (dipakai assets/app/admin/pengaturanhome.js)
  var HOME_ITEM_GROUPS = <?php echo json_encode($grup_item); ?>;
</script>
