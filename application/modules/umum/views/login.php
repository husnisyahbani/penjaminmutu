    <!-- Page -->
    <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">>
      <div class="page-content vertical-align-middle">
        <div class="panel">
          <div class="panel-body">
            <div class="brand">
              <img class="brand-img" src="<?php echo asset_url();?>/assets/images/logostik.png" alt="STIK SITI KHADIJAH" width="120px">
              <h2 class="brand-text font-size-18">Sistem Informasi Penjamin Mutu</h2>
            </div>
            <form method="post" action="<?php echo base_url("login") ?>" autocomplete="off">
              <div class="form-group form-material floating" data-plugin="formMaterial">
                <input type="username" class="form-control" name="username" />
                <label class="floating-label">Username</label>
              </div>
              <div class="form-group form-material floating" data-plugin="formMaterial">
                <input type="password" class="form-control" name="password" />
                <label class="floating-label">Password</label>
              </div>
              
              <button type="submit" class="btn btn-primary btn-block btn-lg mt-40" name="submitbutton" value="submit">Sign in</button>
              <!--<button type="button" class="btn btn-danger btn-block btn-lg mt-20" id="btnkonsultasi">Konsultasi</button>-->
            </form>
            
          </div>
        </div>

        <footer class="page-copyright page-copyright-inverse">
          <p>WEBSITE BY STIK SITI KHADIJAH</p>
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