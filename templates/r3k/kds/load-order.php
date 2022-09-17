
<?php
use Src\TableGateways\OrdersGateway;
use Src\Classes\Order;

$orderstGateway = new OrdersGateway($dbConnection);
$datas =$orderstGateway->GetItemsSold();

$PageTitle = "KDS System Order ";
include "../$templatePath/head.php";
include "../$templatePath/header.php";
?>
<body class="wide ">
    <div class="container">
        <div class="row"><div class="col-12">
            <?php echo json_encode($datas)?>
        </div></div>
    </div>
<body>
</html>