<?php

use Src\TableGateways\OrdersGateway;
use Src\Classes\Order;
use Src\Classes\KMail;

$orderstGateway = new OrdersGateway($dbConnection);
$datas = $orderstGateway->GetItemsSold();
$str = json_encode($datas);
//$secret = $userLogin->GetEncryptedKey('rabih@kbs-leb.com');
//KMail::sendResetPasswordMail("rabih@kbs-leb.com",$secret);
$PageTitle = "KDS System Order ";
include "../$templatePath/head.php";
include "../$templatePath/header.php";
?>

<body class="wide ">
<div class="full-div bgimg">
<div class="full-div bg-warning opacity-min">
<?php throw new Exception('Uncaught Exception');
echo "Not Executed\n"; ?>
</div>
</div>

</body>
<?php include "../$templatePath/footer.php"; ?>
</html>