<script type="text/javascript">


</script>
<!-- Navbar (sticky bottom) -->

<header class="sticky-top bg-custom opacity-90 p2 text-light">
  <div class="row d-md-flex d-none">
    <div class="col-3">
      <img class="mx-4" src="/media/System/logo.svg" alt="" width="70" height="70">
    </div>
    <div class="col-6">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <p id='time' class="text-center"></p>
            <script>
              updateTime();
            </script>
          </div>
        </div>
      </div>
    </div>
    <div class="col-3 justify-content-end pull-right text-right">
      <div class="dropdown show">
        <a class="btn btn-lg dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-user fa-3" aria-hidden="true"></i>
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
          <?php $navClass = "navbar-nav";
          include "pages/header-content.php"; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row d-md-none">
    <div class="col-12">
      <button class="btn text-light" type="button" data-toggle="collapse" data-target="#navbarCollapse">
        <i class="fa fa-bars"></i>
      </button>
    </div>
    <div class="col-12">
      <div class="container">
        <div class="collapse" id="navbarCollapse">
          <?php $navClass = "navbar-nav";
          include "pages/header-content.php"; ?>
        </div>
      </div>
    </div>
  </div>
</header>

<script src="//code.tidio.co/cf02wzadzvhfykn8u60bxpjnuw5niber.js" async></script>