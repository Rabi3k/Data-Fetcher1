<!-- Footer -->

<footer class="sticky-bottom  text-light bg-dark opacity-min p2">
  <div class="row">
    <div class="col-3">

      <span class="" <?php if ($userLogin->checkLogin()) { ?> onclick="toogleNav()" <?php } ?>>
        <img class="mx-4" src="/media/System/logo.svg" alt="" width="70" height="70">
      </span>
    </div>
    <div class="col-6 text-center">
      <p id='time' class="text-center time-text"></p>
    </div>
    <div class="col-3 pt-4">
      <p>Powered by <a href="https://funneat.dk" title="Fun'N Eat" target="_blank" class="text-warning">Fun'N Eat</a></p>

    </div>
  </div>
</footer>
<script>
  updateTime();
</script>