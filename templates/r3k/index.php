<?php

//use Src\TableGateways\UserGateway;

if (!$userGateway->checkLogin()) {
  header("Location: $rootpath/login.php");
  exit();
}


$PageTitle = "KDS System";
include  $templatePath . '/head.php';


?>
<!-- Header / Home-->



<body class="wide  bg-dark" id="main">


  <div class="full-div opacity-min bg-secondary">

    <?php
    include  $templatePath . '/header.php';
    include "$templatePath/kds/index.php"
    ?>

  </div>

  <?php
  include  $templatePath . '/sidepanel.php';
  include  $templatePath . '/footer.php';
  ?>
</body>

</html>