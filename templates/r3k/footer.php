<!-- Footer -->

<footer class="sticky-bottom  text-light bg-dark opacity-min p2">
  <div class="row">
    <div class="col-3">

      <span class="" <?php if ($userGateway->checkLogin()) { ?> onclick="toogleNav()" <?php } ?>>
        <img class="mx-4 logo-img" src="/media/System/logo.svg" >
      </span>
    </div>
    <div class="col-6 text-center">
      <p id='time' class="text-center time-text pt-3"></p>
    </div>
    <div class="col-3 pt-4">
      <p>Powered by <a href="https://funneat.dk" title="Fun'N Eat" target="_blank" class="text-warning">Fun'N Eat</a></p>

    </div>
  </div>
</footer>
<script type="text/javascript" src="<?php echo $rootpath . "/" . $templatePath ?>/js/post-script.min.js"></script>
<script>
  updateTime();
</script>