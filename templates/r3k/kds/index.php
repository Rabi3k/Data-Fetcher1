<?php
//require "../bootstrap.php";
use Src\TableGateways\RequestsGateway;
if(!$userLogin->checkLogin())
{
    header("Location: $rootpath/login.php");
    exit();
}
$PageTitle = "KDS System";
include "../$templatePath/head.php";
?>



<?php
include "../$templatePath/header.php";
?>
<body class="wide bgimg">
<script type='text/javascript'>
var interval = setInterval( function(){myFunction();},1*1000);
</script>


<div class="card-columns" id="orderCards">
<?php
include "load-cards.php";
?>

</div>
<?php
//include  __DIR__.'/footer.php';
?>
</body>
</html>