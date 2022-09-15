
<?php
use Src\TableGateways\OrdersGateway;
use Src\Classes\Order;


$orderId =  $_GET['id'];
if(!$orderId)
{
    die("OrderId is Not Provided");
}
$orderstGateway = new OrdersGateway($dbConnection);
$datas =$orderstGateway->FindById($orderId);
$dataE = (array)end($datas);
/*var_dump($datasJ);
echo $datasJ->id;*/
if(!$dataE)
{
    $PageTitle = "Order Not Found!";
    include "../$templatePath/head.php";

    die("Order Not Found!");
}
$OrderClass = new Order($dataE);
//$OrderClass->setFromJsonStr($datas);
//echo $OrderClass->id;
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
//echo($OrderClass2->getJsonStr());
//$OrderClass = new Order($datas);
//echo $OrderClass->getJsonStr();
$OrderClass2->setFromJsonStr($OrderClass->getJsonStr());
echo($OrderClass2->id);
//var_dump($OrderClass2);
?>
        </div></div>
    </div>
<body>
</html>