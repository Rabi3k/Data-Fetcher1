<?php

$errorMessage = false;
$lUser = null;

if (isset($_GET['secret'])) {
  $keys = $userGateway->DecryptSecretKey($_GET['secret']);
  if (isset($keys) && count($keys) > 1) {
    $lUser = $userGateway->GetUserByUsernameSecretKey($keys[0], $keys[1]);
  }
  if (!isset($lUser)) {
    header("Location: /");
    exit();
  } else {
    if (!$userGateway->ValidateLoginBySecretKey($keys[0], $keys[1])) {
      //show error message;
      $errorMessage = true;
    } else {
      header("Location: \\");
      exit();
    }
  }
} else if (isset($_POST['uname']) && isset($_POST['password'])) {
  if (!$userGateway->ValidateLogin($_POST['uname'], $_POST['password'])) {
    //show error message;
    $errorMessage = true;
  }
}


if ($userGateway->checkLogin()) {
  $returnUrl = "$rootpath/";
  if (isset($_GET['returnurl'])) {
    $returnUrl = $_GET['returnurl'];
  }
  header("Location: $returnUrl");
  exit();
}
$PageTitle = "KDS System Login";
include  $templatePath . '/head.php';

?>

<body class="wide  bgimg">
  <?php include  $templatePath . '/header.php'; ?>

  <div class="full-div opacity-90 bg-dark">
    <div class="display-middle text-white text-center">


      <form class="form-signin" method="post">
        <img class="mb-4" src="/media/System/logo.svg" alt="logo" width="150" height="150">
        <p class="h3 mb-3 font-weight-normal">Please sign in</span>
        <div class="form-group">
          <label for="inputEmail" class="sr-only">UserName / Email address</label>
          <input type="text" id="inputEmail" class="form-control" name="uname" placeholder="Username / Email address" required autofocus>
        </div>
        <div class="form-group">
          <label for="inputPassword" class="sr-only">Password</label>
          <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="checkbox mb-3">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <button class="btn btn-lg btn-custom btn-block" type="submit">Sign in</button>
      </form>

    </div>

  </div>
  </div>

  <?php
  include  $templatePath . '/footer.php';
  ?>
</body>

</html>