<?php

use Src\TableGateways\OrdersGateway;
use Src\Classes\Order;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $orderstGateway = new OrdersGateway($dbConnection);
    $datas = $orderstGateway->FindById($id);
    $dataE = (array)end($datas);
    $row = new Order($dataE);
}
if (!isset($row)) {
    die();
}
$OrderDate = new \DateTime($row->fulfill_at ?? $row->updated_at);
$OrderDate->setTimezone(new \DateTimeZone($row->restaurant_timezone));
$jDate = $OrderDate->format('Y/m/d H:i:s');
$oDate = $OrderDate->format('l-M-y  H:i:s');
$todayW = (new \DateTime())->setTimestamp(strtotime("-10 minutes"));
$todayW->setTimezone(new \DateTimeZone($row->restaurant_timezone));
$today = new \DateTime();
$today->setTimezone(new \DateTimeZone($row->restaurant_timezone));
$bgClass = 'bg-info';

$orderTypeIcon = "<span></span>";
$orderTypeText = "<span></span>";
switch ($row->type) {
    case 'pickup':
        $orderTypeIcon = '<i class="h3 fa fa-shopping-basket"></i>';
        $orderTypeText = 'Afhentning';
        break;
    case 'delivery':
        $orderTypeIcon = '<i class="h3 fa fa-truck"></i>';
        $orderTypeText = 'Levering';
        break;
    case 'order_ahead':
        $orderTypeIcon = '<i class="h3 fa fa-coffee"></i>';
        $orderTypeText = 'Bordreservation';
        break;
    case 'table_reservation':
        $orderTypeIcon = '<i class="h3 fa fa-coffee"></i>';
        $orderTypeText = 'Bordreservation';
        break;
    case 'dine_in':
        $orderTypeIcon = '<i class="h3 fa fa-user"></i>';
        $orderTypeText = 'Spise i';
        break;
    default:
        break;
}
?>
<div id='accordion_<?php echo $row->id ?>' class="swiper-slide" tag="<?php echo $row->restaurant_id ?>" order-type="<?php echo $orderTypeText?>">
    <div class="cards-wrapper" tag="<?php echo $row->id ?>">
        <div class='card opacity-90'>
            <div class='card-header <?php echo $bgClass ?> text-light py-0' aria-expanded='true' aria-controls='collapse_<?php echo $row->id ?>'>
                <input type='hidden' name="OrderDate" id='OrderDate_<?php echo $row->id ?>' value='<?php echo $jDate ?>' />
                <input type='hidden' name="OrderStatus" id='OrderStatus_<?php echo $row->id ?>' value='<?php echo $bgClass ?>' />
                <div class="row">
                    <div class='col-1 '>

                        <?php echo $orderTypeIcon ?>
                    </div>
                    <div class='col-6 '>
                        <span><?php echo $orderTypeText ?></span><br/>
                        <span>Gæster:<?php echo $row->persons ?></span>
                    </div>

                    <div class='col-5 text-right'>
                        <!-- <span>Payment: <?php //echo $row->payment 
                                            ?></span> -->
                        <span><?php echo $OrderDate->format('h:i a'); ?></span>
                    </div>
                    <div class='col-12 text-right'>
                        <span>Kunde: <?php echo $row->client_first_name . " " . $row->client_last_name; ?></span>
                    </div>
                </div>
            </div>
            <div id='collapse_<?php echo $row->id ?>' class=' card-body bg-white'>
                
                <ul class='list-group list-group-flush text-dark'>
                    <?php
                    if ($row->instructions != '') { ?>
                        <li class='list-group-item'>
                            <div class="row d-flex justify-content-center">
                                <div class="12"><span class="text-danger"><strong>=! <?php echo $row->instructions ?> !=</strong></span></div>
                            </div>
                        </li>
                        <?php }
                    if (count($row->items) == 0) { ?>
                        <li class='list-group-item'>
                            <div class="row d-flex justify-content-center">
                                <div class="12"><span class="text-dark"><strong>Ingen ordre endnu!</strong></span></div>
                            </div>
                        </li>
                        <?php }
                    foreach ($row->items as $item) {
                        if ($item->type === "item") { ?>
                            <li class='list-group-item'>
                                <div class="row">
                                    <div class="3"><?php echo $item->quantity ?> - </div>
                                    <div class="9"><span><?php echo $item->name ?></span></div>
                                </div>
                                <div class='row'>
                                    <ul class='list-group list-group-flush bg-white text-secondary'>
                                        <?php if ($item->instructions) { ?>
                                            <li class='list-group-item'>
                                                <span><?php echo $item->instructions ?></span>
                                            </li>
                                        <?php }
                                        foreach ($item->options as $option) { ?>
                                            <li class='list-group-item'>
                                                <span><?php echo $option->quantity . "-" . $option->group_name . " / " . $option->name ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
            <div class="card-footer py-0">
                <div class="row">
                    <div class="col-1">

                    </div>
                    <div class="col-5">
                        <span>#<?php echo $row->id ?></span>
                    </div>
                    <div class="col-6 text-right">
                        <span class="time-remaining font-weight-bold"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>