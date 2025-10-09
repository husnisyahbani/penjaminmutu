    <!-- Page -->
    <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">>
      <div class="page-content vertical-align-middle">
        <div class="panel">
          <div class="panel-body">
            <div class="brand">
              <h2 class="brand-text font-size-18">Konsultasi</h2>
            </div>
            <form id="formkonsultasi" method="post" action="<?php echo base_url("umum/konsultasi") ?>" autocomplete="off" enctype="multipart/form-data">
              <div class="form-group form-material floating" data-plugin="formMaterial">
                <input type="text" class="form-control" name="konsul_judul"  data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"/>
                <label class="floating-label">Judul</label>
              </div>
              <div class="form-group form-material floating" data-plugin="formMaterial">
                <input type="email" class="form-control" name="konsul_email"  data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"/>
                <label class="floating-label">Email</label>
              </div>

              <div class="form-group form-material floating" data-plugin="formMaterial">
              <textarea class="form-control" id="konsul_isi" name="konsul_isi" rows="6" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"></textarea>
                <label class="floating-label">Isi</label>
              </div>

              <div class="form-group form-material floating" data-plugin="formMaterial">
              <label class="floating-label">File</label>
                <input type="file" id="upload_file" name="upload_file" data-plugin="dropify"/>
                
              </div>

              <div class="form-group form-material floating" data-plugin="formMaterial">
              
              <input type="text" class="form-control" name="captcha"  data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"/>
                                           <label class="floating-label">Captcha</label>
              
                
              </div>
              <?php echo $captcha; ?>
             
              <button id="validateButton" type="submit" class="btn btn-primary btn-block btn-lg mt-40" name="submitbutton" value="submit">Kirim</button>
             </form>
            
          </div>
        </div>

        <footer class="page-copyright page-copyright-inverse">
          <p>WEBSITE BY DINAS PENDIDIKAN PROVINSI SUMATERA SELATAN</p>
          <p>© 2021. All RIGHT RESERVED.</p>
          <!-- <div class="social">
            <a class="btn btn-icon btn-pure" href="javascript:void(0)">
            <i class="icon bd-twitter" aria-hidden="true"></i>
          </a>
            <a class="btn btn-icon btn-pure" href="javascript:void(0)">
            <i class="icon bd-facebook" aria-hidden="true"></i>
          </a>
            <a class="btn btn-icon btn-pure" href="javascript:void(0)">
            <i class="icon bd-google-plus" aria-hidden="true"></i>
          </a>
          </div> -->
        </footer>
      </div>
    </div>
    <!-- End Page -->