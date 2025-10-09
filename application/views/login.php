<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="author" content="">
    
    <title>Login </title>
    
    <link rel="apple-touch-icon" href="https://stik-sitikhadijah.ac.id/wp-content/uploads/2019/05/Logo-Sekolah-Tinggi-Ilmu-Kesehatan-Siti-Khadijah.png">
    <link rel="shortcut icon" href="https://stik-sitikhadijah.ac.id/wp-content/uploads/2019/05/Logo-Sekolah-Tinggi-Ilmu-Kesehatan-Siti-Khadijah.png">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo asset_url() . 'global/css/bootstrap.min.css'; ?>">
    <link rel="stylesheet" href="<?php echo asset_url() . 'global/css/bootstrap-extend.min.css'; ?>">
    <link rel="stylesheet" href="<?php echo asset_url() . 'assets/css/site.min.css'; ?>">
    
    <!-- Plugins -->
    <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/animsition/animsition.css'; ?>">
    <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/asscrollable/asScrollable.css'; ?>">
    <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/switchery/switchery.css'; ?>">
    <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/intro-js/introjs.css'; ?>">
    <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/slidepanel/slidePanel.css'; ?>">
    <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/flag-icon-css/flag-icon.css'; ?>">
    <link rel="stylesheet" href="<?php echo asset_url() . 'assets/examples/css/pages/login.css'; ?>">
    <link rel="stylesheet" href="<?php echo asset_url() . 'global/vendor/toastr/toastr.css'; ?>">
    <link rel="stylesheet" href="<?php echo asset_url() . 'assets/examples/css/advanced/toastr.css'; ?>">
    
    
    <!-- Fonts -->
    <link rel="stylesheet" href="<?php echo asset_url() . 'global/fonts/web-icons/web-icons.min.css'; ?>">
    <link rel="stylesheet" href="<?php echo asset_url() . 'global/fonts/brand-icons/brand-icons.min.css'; ?>">
    <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
    
    <!--[if lt IE 9]>
    <script src="../../../global/vendor/html5shiv/html5shiv.min.js"></script>
    <![endif]-->
    
    <!--[if lt IE 10]>
    <script src="../../../global/vendor/media-match/media.match.min.js"></script>
    <script src="../../../global/vendor/respond/respond.min.js"></script>
    <![endif]-->
    
    <!-- Scripts -->
    <script src="<?php echo asset_url() . 'global/vendor/breakpoints/breakpoints.js'; ?>"></script>
    <script>
      Breakpoints();
    </script>
  </head>
  <body class="animsition page-login layout-full page-dark">
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->


    <!-- Page -->
    <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">>
      <div class="page-content vertical-align-middle animation-slide-top animation-duration-1">
        <div class="brand">
          <img class="brand-img" src="https://stik-sitikhadijah.ac.id/wp-content/uploads/2019/05/Logo-Sekolah-Tinggi-Ilmu-Kesehatan-Siti-Khadijah.png" alt="...">
<!--          <h2 class="brand-text">Remark</h2>-->
        </div>
<!--        <p>Sign into your pages account</p>-->
        <form method="post" action="<?php echo base_url() ?>login" method="post" enctype="multipart/form-data">
          
          <div class="form-group">
            <label class="sr-only" for="inputEmail">Email</label>
            <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email">
          </div>
          <div class="form-group">
            <label class="sr-only" for="inputPassword">Password</label>
            <input type="password" class="form-control" id="inputPassword" name="password"
              placeholder="Password">
          </div>
            <button type="submit" class="btn btn-primary btn-block" name="submit" value="submit">Sign in</button>
        </form>
        

        <footer class="page-copyright page-copyright-inverse">
            <p>WEBSITE BY <a href="https://stik-sitikhadijah.ac.id">STIK SITI KHADIJAH</a></p>
          <p>© 2019. All RIGHT RESERVED.</p>
<!--          <div class="social">
            <a class="btn btn-icon btn-pure" href="javascript:void(0)">
          <i class="icon bd-twitter" aria-hidden="true"></i>
        </a>
            <a class="btn btn-icon btn-pure" href="javascript:void(0)">
          <i class="icon bd-facebook" aria-hidden="true"></i>
        </a>
            <a class="btn btn-icon btn-pure" href="javascript:void(0)">
          <i class="icon bd-dribbble" aria-hidden="true"></i>
        </a>
          </div>-->
        </footer>
      </div>
    </div>
    <!-- End Page -->


    <!-- Core  -->
    <script src="<?php echo asset_url() . 'global/vendor/babel-external-helpers/babel-external-helpers.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/vendor/jquery/jquery.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/vendor/popper-js/umd/popper.min.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/vendor/bootstrap/bootstrap.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/vendor/animsition/animsition.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/vendor/mousewheel/jquery.mousewheel.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/vendor/asscrollbar/jquery-asScrollbar.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/vendor/asscrollable/jquery-asScrollable.js'; ?>"></script>
    
    <!-- Plugins -->
    <script src="<?php echo asset_url() . 'global/vendor/switchery/switchery.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/vendor/intro-js/intro.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/vendor/screenfull/screenfull.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/vendor/slidepanel/jquery-slidePanel.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/vendor/jquery-placeholder/jquery.placeholder.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/vendor/toastr/toastr.js'; ?>"></script>
    
    <!-- Scripts -->
    <script src="<?php echo asset_url() . 'global/js/Component.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/js/Plugin.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/js/Base.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/js/Config.js'; ?>"></script>
    
    <script src="<?php echo asset_url() . 'assets/js/Section/Menubar.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'assets/js/Section/Sidebar.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'assets/js/Section/PageAside.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'assets/js/Plugin/menu.js'; ?>"></script>
    
    <!-- Config -->
    <script src="<?php echo asset_url() . 'global/js/config/colors.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'assets/js/config/tour.js'; ?>"></script>
    <script>Config.set('assets', '../../assets');</script>
    
    <!-- Page -->
    <script src="<?php echo asset_url() . 'assets/js/Site.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/js/Plugin/asscrollable.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/js/Plugin/slidepanel.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/js/Plugin/switchery.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/js/Plugin/jquery-placeholder.js'; ?>"></script>
    <script src="<?php echo asset_url() . 'global/js/Plugin/toastr.js'; ?>"></script>
    
    <script>
     $(function () {
         //alert('hai');
         <?php
if (isset($pesanberhasil)) {
    echo "toastr.options.timeOut = 'false';
        toastr.options.closeButton = true;
        toastr.options.positionClass = 'toast-top-right';
toastr['success']('" . $pesanberhasil . "');";
}
?>
<?php
if (isset($pesanerror)) {
    echo "toastr.options.timeOut = 'false';
        toastr.options.closeButton = true;
        toastr.options.positionClass = 'toast-top-right';
toastr['error']('" . $pesanerror . "');";
}
?>
     });
      (function(document, window, $){
        'use strict';
    
        var Site = window.Site;
        $(document).ready(function(){
            
          Site.run();
          
        });
      })(document, window, jQuery);
    </script>
    
  </body>
</html>
