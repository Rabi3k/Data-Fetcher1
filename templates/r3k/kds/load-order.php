
<?php
use Src\TableGateways\RequestsGateway;
use Src\Classes\Order;


$orderId =  $_GET['id'];
if(!$orderId)
{
    die("OrderId is Not Provided");
}
$requestGateway = new RequestsGateway($dbConnection);
$datas = $requestGateway->RetriveOrder($orderId)[0];
if(!$datas)
{
    $PageTitle = "Order Not Found!";
    include "../$templatePath/head.php";

    die("Order Not Found!");
}
$OrderClass = new Order($datas);
$OrderClass2 = new Order();
//echo json_encode($OrderClass->items[0]["id"])."<br/>";
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$PageTitle = "KDS System Order #".$orderId;
include "../$templatePath/head.php";
include "../$templatePath/header.php";
?>
<body class="wide ">
    <div class="container">
        <div class="row"><div class="col-12">
            <?php 
echo($OrderClass2->getJsonStr());
$OrderClass = new Order($datas);
$OrderClass2->setFromJsonStr($OrderClass->getJsonStr());
echo($OrderClass2->getJsonStr());
?>
        </div></div>
    </div>
<body>
</html>