<script type="text/javascript">


</script>
<!-- Navbar (sticky bottom) -->

<header class="sticky-top bg-custom opacity-90 p2 text-light">
  <div class="row d-md-flex d-none">
    <div class="col-3">
      <img class="mb-4" src="/media/System/logo.svg" alt="" width="100" height="100">
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
        <?php include "pages/header-content.php"; ?>
      </div>
    </div>
    <div class="col-3 justify-content-end pull-right">
      <img class="mb-4 pull-right" src="/media/System/logo.svg" alt="" width="100" height="100">
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
        
          <?php $navClass="navbar-nav"; include "pages/header-content.php"; ?>
        </div>
      </div>
    </div>
  </div>
</header>

<script src="//code.tidio.co/cf02wzadzvhfykn8u60bxpjnuw5niber.js" async></script>