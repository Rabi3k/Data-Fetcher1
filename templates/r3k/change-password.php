<?php

$errorMessage = false;
$passwordChanged = false;
$lUser = null;
if (isset($_GET['secret']))
{
  $keys = $userLogin->DecryptSecretKey($_GET['secret']);
  if(isset($keys) && count($keys)>1)
  {
    $lUser= $userLogin->GetUserByUsernameSecretKey($keys[0],$keys[1]);
  }
}
if(!isset($lUser))
{
  header("Location: /");
  exit();
}
if (isset($_GET['action'])) {
  if ($_GET['action'] == 'change-password') {
    if (isset($_POST['password1']) && isset($_POST['password2'])) {
      $userLogin->UpdateUserPassword($lUser, $_POST['password1']);
      $passwordChanged = true;
    }
  }
}
/*
if ($userLogin->checkLogin()) {
  $returnUrl = "$rootpath/";
  if (isset($_GET['returnurl'])) {
    $returnUrl = $_GET['returnurl'];
    
    header("Location: $returnUrl");
  } else {
    header("Location: $returnUrl");
  }
  exit();
}*/
$PageTitle = "KDS System Login";
include  $templatePath . '/head.php';

?>
<!-- Icons -->
<script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>

<body class="wide  bgimg">
  <?php include  $templatePath . '/header.php'; ?>

  <div class="full-div opacity-90 bg-dark">

    <div class="container display-middle text-white ">
      <div class="row">
        <?php if (!$passwordChanged) { ?>
          <div class="col-6 offset-2 text-center">
            <form method="post" id="passwordForm" action="<?php echo $_SERVER['REQUEST_URI'] ?>&action=change-password">
              <div class="col-12 p-2">
                <input type="password" class="input-lg form-control" name="password1" id="password1" placeholder="New Password" autocomplete="off">
              </div>
              <div class="col-12 p-2">
                <input type="password" class="input-lg form-control" name="password2" id="password2" placeholder="Repeat Password" autocomplete="off">
              </div>
              <div class="p-2">
                <input type="submit" class="btn btn-primary btn-load btn-lg" data-loading-text="Changing Password..." value="Change Password">
              </div>
            </form>
          </div>
          <div class="col-4">
            <span id="8char" data-feather="x"></span> 8 Characters Long<br>
            <span id="ucase" data-feather="x"></span> One Uppercase Letter<br>
            <span id="lcase" data-feather="x"></span> One Lowercase Letter<br>
            <span id="num" data-feather="x"></span> One Number<br>
            <span id="pwmatch" data-feather="x"></span> Passwords Match
          </div>
        <?php } else { ?>
          <div class="col-9 offset-4 text-center">
          <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Password Changed Succesfully!</h4>
            you can now login using the new password!
          </div>
        </div>
        <?php } ?>
      </div>


    </div>

  </div>
  </div>
  <script src="/kds-admin/src/js/script.min.js"></script>
  <script>
    feather.replace()
  </script>
  <?php
  include  $templatePath . '/footer.php';
  ?>
</body>

</html>