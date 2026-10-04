<?php
/**
 * Halaman login - tema halaman depan (homepage).
 * Shell (navbar & footer) dipakai dari umum/includes/hp_top & hp_bottom.
 */

$nama_sistem = isset($home) ? $home->val('site_nama', 'SIJAMU') : 'SIJAMU';

$this->load->view('umum/includes/hp_top', array(
    'title' => 'Login - ' . $nama_sistem,
    'page'  => 'login',
    'home'  => $home,
));
?>

  <section class="hp-hero hp-hero--page hp-auth">
    <div class="container">

      <div class="hp-auth__card" data-reveal>

        <!-- SISI KIRI: identitas & manfaat -->
        <aside class="hp-auth__aside">
          <div class="hp-auth__brand">
            <img src="<?php echo $home->media('site_logo', base_url('assets/assets/images/logostik.png')); ?>" alt="Logo <?php echo htmlspecialchars($home->val('site_institusi', 'STIK Siti Khadijah')); ?>">
            <span>
              <strong><?php echo htmlspecialchars($nama_sistem); ?></strong>
              <span><?php echo htmlspecialchars($home->val('site_subjudul', '')); ?></span>
            </span>
          </div>

          <h2>Masuk ke Sistem Informasi Penjaminan Mutu</h2>
          <p>Satu pintu untuk audit mutu internal, tindak lanjut, dan pemantauan mutu di lingkungan <?php echo htmlspecialchars($home->val('site_institusi', 'STIK Siti Khadijah')); ?>.</p>

          <ul class="hp-auth__list">
            <li><i class="bi bi-check-circle-fill"></i> Penetapan, Pelaksanaan, Evaluasi, Pengendalian & Peningkatan</li>
            <li><i class="bi bi-check-circle-fill"></i> Dokumen SPMI dalam satu tempat</li>
            <li><i class="bi bi-check-circle-fill"></i> Akses sesuai peran: PPM, Auditor, dan Auditee</li>
          </ul>

          <a class="hp-auth__kembali" href="<?php echo base_url(); ?>">
            <i class="bi bi-arrow-left"></i> Kembali ke beranda
          </a>
        </aside>

        <!-- SISI KANAN: formulir -->
        <div class="hp-auth__body">
          <span class="hp-eyebrow"><i class="bi bi-shield-lock"></i> Login</span>
          <h2 class="hp-auth__title">Selamat datang kembali</h2>
          <p class="hp-auth__sub">Silakan masukkan username dan password akun Anda.</p>

          <?php if (!empty($pesanerror)): ?>
            <div class="hp-auth__alert hp-auth__alert--error">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <?php echo htmlspecialchars($pesanerror); ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($pesanberhasil)): ?>
            <div class="hp-auth__alert hp-auth__alert--sukses">
              <i class="bi bi-check-circle-fill"></i>
              <?php echo htmlspecialchars($pesanberhasil); ?>
            </div>
          <?php endif; ?>

          <form method="post" action="<?php echo base_url('login'); ?>" autocomplete="off">
            <div class="hp-field">
              <label class="hp-field__label" for="username">Username</label>
              <input class="hp-field__input" type="text" id="username" name="username" placeholder="Masukkan username" required autofocus>
            </div>

            <div class="hp-field">
              <label class="hp-field__label" for="password">Password</label>
              <input class="hp-field__input" type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="hp-btn hp-btn--primer w-100 justify-content-center mt-2" name="submitbutton" value="submit">
              <i class="bi bi-box-arrow-in-right"></i> Masuk
            </button>
          </form>
        </div>

      </div>

    </div>
  </section>

<?php $this->load->view('umum/includes/hp_bottom', array('home' => $home)); ?>
