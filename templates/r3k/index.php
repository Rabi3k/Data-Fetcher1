<?php


if (!$userLogin->checkLogin()) {
  header("Location: $rootpath/login.php");
  exit();
}


$PageTitle = "KDS System";
include  $templatePath . '/head.php';

include  $templatePath . '/header.php';
?>
<!-- Header / Home-->



<body class="wide  bgimg">
  <div class="full-div opacity-min bg-secondary">
    <?php include "$templatePath/kds/index.php" ?>

  </div>

  <?php
  include  $templatePath . '/footer.php';
  ?>
</body>

</html>