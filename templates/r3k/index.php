<?php
$PageTitle = "KDS System";
include  $templatePath.'/head.php';

include  $templatePath.'/header.php';
?>
<!-- Header / Home-->



<body class="wide bgimg">

  <div class="display-middle text-white text-center">
    <h1 class="text-jumbo">KDS System</h1>
    <?php if(!$userLogin->checkLogin()) { ?>
    <h2>
      Is your kitchen busy all time?<br/>
      Try our solution!<br/>
      <a type="button" class="btn btn-secondary btn-sm" href="<?php echo $rootpath?>/kds">Click Here</a>
    </h2>
      <?php } ?>
    
  </div>


<?php
include  $templatePath.'/footer.php';
?>
</body>
</html>