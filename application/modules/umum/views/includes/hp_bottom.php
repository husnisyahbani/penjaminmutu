<?php
/**
 * Shell bawah halaman turunan: login & sub halaman SPMI.
 * Menutup <main>, memuat footer, tombol kembali ke atas, Bootstrap bundle,
 * dan assets/desain/home.js (perilaku navbar sticky, reveal, back-to-top).
 *
 * Variabel masukan:
 *   $home  HomeModel (opsional, dimuat otomatis bila tidak ada)
 *   $page  penanda halaman aktif: '' | 'login' | 'spmi'
 *   $tail  tambahan HTML mentah sebelum </body> (opsional, mis. skrip halaman)
 */

$this->load->helper('hp');

if (!isset($home)) {
    $this->load->model('HomeModel', 'home');
    $home = $this->home;
}

$institusi = $home->val('site_institusi', 'STIK Siti Khadijah');
$nama      = $home->val('site_nama', 'SIJAMU');
$subjudul  = $home->val('site_subjudul', '');
$logo      = $home->media('site_logo', base_url('assets/assets/images/logostik.png'));

$menu_section = array(
    array('id' => 'profil', 'label' => 'Profil', 'tampil' => $home->on('section_profil')),
    array('id' => 'visi-misi', 'label' => 'Visi & Misi', 'tampil' => $home->on('section_visi')),
    array('id' => 'tupoksi', 'label' => 'Tupoksi', 'tampil' => $home->on('section_tupoksi')),
    array('id' => 'sasaran-mutu', 'label' => 'Sasaran Mutu', 'tampil' => $home->on('section_sasaran')),
    array('id' => 'pengelola', 'label' => 'Pengelola', 'tampil' => $home->on('section_pengelola')),
    array('id' => 'struktur', 'label' => 'Struktur Organisasi', 'tampil' => $home->on('section_struktur')),
    array('id' => 'sk', 'label' => 'Surat Keputusan', 'tampil' => $home->on('section_sk')),
);

$spmi_menu = array(
    array('slug' => 'penetapan', 'label' => 'Penetapan'),
    array('slug' => 'pelaksanaan', 'label' => 'Pelaksanaan'),
    array('slug' => 'evaluasi', 'label' => 'Evaluasi'),
    array('slug' => 'pengendalian', 'label' => 'Pengendalian'),
    array('slug' => 'peningkatan', 'label' => 'Peningkatan'),
);
?>

</main>

<!-- ================= FOOTER ================= -->
<footer class="hp-footer">
  <div class="container">
    <div class="row g-4">

      <div class="col-lg-5">
        <div class="hp-footer__brand">
          <img src="<?php echo $logo; ?>" alt="Logo <?php echo htmlspecialchars($institusi); ?>">
          <span>
            <strong><?php echo htmlspecialchars($nama); ?></strong>
            <span><?php echo htmlspecialchars($subjudul); ?></span>
          </span>
        </div>
        <p><?php echo htmlspecialchars(hp_potong($home->val('hero_deskripsi', ''), 26)); ?></p>
        <div class="hp-footer__social">
          <a href="mailto:<?php echo htmlspecialchars($home->val('kontak_email')); ?>" title="Email"><i class="bi bi-envelope"></i></a>
          <a href="<?php echo htmlspecialchars($home->val('kontak_website')); ?>" target="_blank" rel="noopener" title="Website"><i class="bi bi-globe2"></i></a>
          <a href="<?php echo base_url('login'); ?>" title="Login"><i class="bi bi-box-arrow-in-right"></i></a>
        </div>
      </div>

      <div class="col-6 col-lg-2">
        <h4>Profil</h4>
        <ul class="hp-footer__links">
          <?php foreach ($menu_section as $menu): ?>
            <?php if (!$menu['tampil']) { continue; } ?>
            <li><a href="<?php echo base_url() . '#' . $menu['id']; ?>"><?php echo htmlspecialchars($menu['label']); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="col-6 col-lg-2">
        <h4>SPMI</h4>
        <ul class="hp-footer__links">
          <?php foreach ($spmi_menu as $menu): ?>
            <li><a href="<?php echo base_url($menu['slug']); ?>"><?php echo htmlspecialchars($menu['label']); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="col-lg-3">
        <h4>Kontak</h4>
        <ul class="hp-footer__links">
          <li><i class="bi bi-geo-alt me-2"></i><?php echo htmlspecialchars(hp_potong($home->val('kontak_alamat'), 16)); ?></li>
          <li><i class="bi bi-telephone me-2"></i><?php echo htmlspecialchars($home->val('kontak_telepon')); ?></li>
          <li><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($home->val('kontak_email')); ?></li>
        </ul>
      </div>

    </div>

    <div class="hp-footer__bottom">
      <span>
        <?php echo htmlspecialchars($home->val('footer_teks')); ?>
        <a href="<?php echo htmlspecialchars($home->val('kontak_website')); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($institusi); ?></a>
      </span>
      <span>&copy; <span data-tahun><?php echo date('Y'); ?></span> <?php echo htmlspecialchars($home->val('footer_kredit')); ?></span>
    </div>
  </div>
</footer>

<button class="hp-top" id="hpTop" type="button" title="Ke atas" aria-label="Kembali ke atas">
  <i class="bi bi-arrow-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo asset_url(); ?>desain/home.js?v=1.0.0"></script>
<?php if (!empty($tail)) { echo $tail; } ?>
</body>
</html>
