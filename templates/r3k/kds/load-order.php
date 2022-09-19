<?php

use Src\TableGateways\OrdersGateway;
use Src\Classes\Order;
use Src\Classes\KMail;

$orderstGateway = new OrdersGateway($dbConnection);
$datas = $orderstGateway->GetItemsSold();
$secret = $userLogin->GetEncryptedKey('rabih@kbs-leb.com');
KMail::sendResetPasswordMail("rabih@kbs-leb.com",$secret);
$PageTitle = "KDS System Order ";
include "../$templatePath/head.php";
include "../$templatePath/header.php";
?>

<body class="wide ">
    <div class="container">
        <div class="row">
            <div class="col-12 card">
                <?php echo json_encode($datas) ?>
            </div>
            <div class="col-12 card">
                <span>http://localhost.kds.int:8060/change-password.php?secret=0x<?php echo bin2hex($secret); ?><span><br/>
                0xdc8fab56980abb93cb94182d54f2fb79b9dffb952256cd6a5224aa289f4d368b136aae6458de1ab82cc1bb50eb572712
            </div>
        </div>
    </div>
    <body>

        </html>