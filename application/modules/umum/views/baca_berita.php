<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $berita_judul ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
  background:#f5f6fa;
}
.sidebar-thumb h6{
  font-size:14px;
  max-height:100px;
  overflow:hidden;
}
.sidebar-thumb img{
  width:100%;
  height:100%;
  object-fit:cover;
}
</style>

</head>
<body>

<div class="container mt-4 mb-5">

  <div class="row">

    <!-- ================= MAIN CONTENT ================= -->
    <div class="col-md-8">

      <div class="card shadow-sm" style="border-radius:10px;overflow:hidden;">
        
        <img src="<?= base_url('filedata').'/'.$berita['berita_file'] ?>" 
             style="width:100%;height:380px;object-fit:cover;">

        <div class="card-body">

          <span style="font-size:13px;color:#888;">
            <?= $berita['berita_create'] ?>
          </span>

          <h2 style="margin-top:10px;">
            <?= $berita['berita_judul'] ?>
          </h2>

          <hr>

          <!-- Jika punya isi berita -->
          <p style="font-size:16px;color:#444;line-height:1.7;">
            <?= isset($berita['berita_isi']) ? $berita['berita_isi'] : '' ?>
          </p>

        </div>

      </div>

    </div>


    <!-- ================= SIDEBAR BERITA LAIN ================= -->
    <div class="col-md-4">

      <h4 class="mb-3">Berita Lainnya</h4>

      <?php if(!empty($result)) { ?>
        <?php foreach($result as $r){ ?>

          <a href="<?= base_url('berita/detail/'.$r['berita_id']) ?>" 
             style="text-decoration:none;color:#000;">

            <div class="card mb-3 shadow-sm sidebar-thumb" 
                 style="border-radius:10px;overflow:hidden;">

              <div class="row g-0">

                <div class="col-4">
                  <img src="<?= base_url('filedata').'/'.$r['berita_file'] ?>">
                </div>

                <div class="col-8">
                  <div class="card-body p-2">

                    <small style="font-size:11px;color:#888;">
                      <?= $r['berita_create'] ?>
                    </small>

                    <h6 class="mt-1">
                      <?= $r['berita_judul'] ?>
                    </h6>

                  </div>
                </div>

              </div>

            </div>

          </a>

        <?php } ?>
      <?php } else { ?>
        <p>Tidak ada berita lain.</p>
      <?php } ?>

    </div>

  </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
