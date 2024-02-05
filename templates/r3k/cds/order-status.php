<?php

use Src\Classes\Order;
use Src\TableGateways\OrdersGateway;

if (isset($_GET['id'])) {
    $orderId =  $_GET['id'];
}
if (!isset($orderId)) {
    $PageTitle = "Order Not Found!";
    $code = 404;
    $httpResponseMessage = $PageTitle;
    include "../$templatePath/head.php";
    include "../$templatePath/header.php";
    include "../$templatePath/error/body.php";
    include "../$templatePath/footer.php";
    exit();
}

$httpResponseMessage = $PageTitle;


$orderstGateway = new OrdersGateway($dbConnection);
$datas = $orderstGateway->FindById($orderId);
//echo json_encode($datas->getJson());
$orderStatus = $datas->ready == 1 ? "Picked up" : ($datas->is_done == 1 ? "Ready to pikup" : "On doing");
switch ($datas->type) {
    case 'delivery':
        $orderStatus = $datas->ready == 1 ? "Deliverd" : ($datas->is_done == 1 ? "Ready for delivery" : "On doing");
        # code...
        break;

    default:
        # code...
        break;
}
?>
<!doctype html>
<html lang="en">
<?php include "../$templatePath/head.php"; ?>

<body>
    <header>
        <!-- place navbar here -->
    </header>
    <main>
        <div class="container-fluid <?php echo $datas->ready == 1 ? "bg-dark" : ($datas->is_done == 1 ? "bg-success" : "bg-primary");  ?> vh-100">
            <div class="row text-light pt-5">
                <center>
                    <div class="col">
                        <h3> <?php echo $datas->restaurant_name ?></h3>
                        <h4> <?php echo $datas->restaurant_phone ?></h4>

                    </div>
                </center>
            </div>
            <div class="row text-light pt-5 pb-2">

                <div class="col-6">
                    <p>
                        <span class="fs-6"> Order type:</span>
                        <span class="fs-4"> <?php echo $datas->type ?></span><br />
                        <span class="fs-6"> Client Name:</span>
                        <span class="fs-4 fw-bold"> <?php echo "$datas->client_first_name $datas->client_last_name " ?> </span><br />
                    </p>

                </div>
                <div class="col-6 text-end">
                    <span class="fs-4 "> Status:</span>
                    <span class="fs-4 "> <?php echo $orderStatus  ?></span><br />
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                </div>
                <div class="col-8">
                    <ul class="list-group">
                        <li class="list-group-item card bg-light header">
                            <div class="row">
                                <div class="col name">
                                    <span class="fs-4 fw-bold">
                                        Name
                                    </span>
                                </div>
                                <div class="col-3 qty text-center">
                                    <span class="fs-4 fw-bold">
                                        Qty
                                    </span>
                                </div>

                            </div>

                        </li>
                        <?php foreach ($datas->items as $item) {
                            $bgColor =  $item->status == "Complete" ? "bg-success" : ($item->status == "ToDo" ? "bg-info" : "bg-dark");
                            if ($item->type == "item") {
                                $name =  $item->name;
                                $options = array();
                                foreach ($item->options as $option) {
                                    # code...
                                    if ($option->type == "size") {
                                        $name .=  " ($option->name)";
                                    } else if ($option->type == "option") {
                                        $options[] = $option;
                                    }
                                }
                        ?>
                                <li class="list-group-item card <?php echo $bgColor   ?> text-light">
                                    <div class="row">
                                        <div class="col name">
                                            <span>
                                                <?php echo "$name" ?>
                                            </span>
                                        </div>
                                        <div class="col-3 qty text-center">
                                            <?php echo "$item->quantity" ?>
                                        </div>
                                    </div>
                                    <ul>
                                        <?php foreach ($options as $option) { ?>
                                            <li class='list-group-item item-options border-0 <?php echo $bgColor   ?> text-light'>
                                                <span><?php echo $option->quantity . "-" . $option->group_name . " / " . $option->name ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                        <?php }
                        } ?>
                    </ul>
                </div>
                <div class="col-2">
                </div>
            </div>

        </div>
    </main>
    <footer>
        <!-- place footer here -->
        <?php include "../$templatePath/footer.php"; ?>
    </footer>

</body>

</html>