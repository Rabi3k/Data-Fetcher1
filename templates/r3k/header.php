
<script type="text/javascript">


</script>
<!-- Navbar (sticky bottom) -->

<header class="sticky-top bg-custom opacity-min p2">
  
  <div class="container"><div class="row">
    <div class="col-12">
      <p id ='time' class="text-center"></p>
    </div>
  </div></div>
  <ul class="nav justify-content-center ">

  <li class="nav-item">
    <a class="nav-link" href="<?php echo $rootpath?>/" >Home</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="https://funneat.dk/" target="_blank">Fun'N Eat</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo $rootpath?>/kds">KDS System</a>
  </li>
  <li class="nav-item">
  <?php if(!$userLogin->checkLogin()) { ?>
    <a class="nav-link"  href="<?php echo $rootpath?>/login.php">login</a>
    <?php } else { ?>
    <a class="nav-link"  href="<?php echo $rootpath?>/logout.php">logout</a>
  <?php } ?>
  </li>
</ul>
</header>
