<script type="text/javascript">


</script>
<!-- Navbar (sticky bottom) -->

<header class="sticky-top bg-custom opacity-90 p2 text-light">
  <div class="row">
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
        <div class="row justify-content-center ">
          <ul class="nav">
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $rootpath ?>/">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="https://funneat.dk/" target="_blank">Fun'N Eat</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $rootpath ?>/kds">KDS System</a>
            </li>
            <?php if (!$userLogin->checkLogin()) { ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo $rootpath ?>/login.php">login</a>
              </li>
            <?php } else { ?>
              <?php if ($userLogin->GetUser()->isSuperAdmin) { ?>
                <li class="nav-item">
                  <a class="nav-link" href="/admin">Super Dash</a>
                </li>
              <?php } ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo $rootpath ?>/logout.php">logout</a>
              </li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-3 justify-content-end pull-right">
      <img class="mb-4 pull-right" src="/media/System/logo.svg" alt="" width="100" height="100">
    </div>
  </div>
</header>
<script src="//code.tidio.co/cf02wzadzvhfykn8u60bxpjnuw5niber.js" async></script>