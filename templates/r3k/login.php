<?php

$errorMessage = false;
if (isset($_POST['uname']) && isset($_POST['password'])) {
  echo "sign";
  if (!$userLogin->ValidateLogin($_POST['uname'], $_POST['password'])) {
    //show error message;
    $errorMessage = true;
    echo "Error";
  }
}
if ($userLogin->checkLogin()) {
  $returnUrl = "$rootpath/";
  if (isset($_SERVER['HTTP_REFERER'])) {
    $path = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
    $returnUrl = rawurldecode($path) === rawurldecode("$rootpath/login.php") ? "$rootpath/" : $_SERVER['HTTP_REFERER'];
    /*echo rawurldecode("$rootpath/login.php")."<br/>";
  echo rawurldecode($_SERVER['HTTP_REFERER'])."<br/>";
  echo $returnUrl;*/
    header("Location: $returnUrl");
  } else {
    header("Location: $returnUrl");
  }
  exit();
}
$PageTitle = "KDS System Login";
include  $templatePath . '/head.php';

?>

<body class="wide  bgimg">
<?php include  $templatePath . '/header.php'; ?>

  <div class="full-div opacity-90 bg-dark">
    <div class="display-middle text-white text-center">


      <form class="form-signin" method="post" >
        <img class="mb-4" src="/media/System/logo.svg" alt="logo" width="150" height="150">
        <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" class="form-control" name="uname" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
        <div class="checkbox mb-3">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <button class="btn btn-lg btn-warning btn-block" type="submit">Sign in</button>
      </form>

    </div>

  </div>
  </div>

  <?php
  include  $templatePath . '/footer.php';
  ?>
</body>

</html>