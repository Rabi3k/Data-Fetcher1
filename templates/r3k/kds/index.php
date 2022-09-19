<?php
//require "../bootstrap.php";
use Src\TableGateways\RequestsGateway;
if(!$userLogin->checkLogin())
{
    header("Location: $rootpath/login.php?returnurl=/kds");
    exit();
}
$PageTitle = "KDS System";
include "../$templatePath/head.php";
?>



<?php
include "../$templatePath/header.php";
?>
<body class="wide">
<div class="full-div bgimg ">
    <div class="full-div  opacity-min bg-light">
<script type='text/javascript'>
var interval = setInterval( function(){myFunction();},5*1000);
</script>


<div class="card-columns" id="orderCards">
<?php
include "load-cards.php";
?>

</div></div>
<?php
//include  __DIR__.'/footer.php';
?>
</body>
</html>