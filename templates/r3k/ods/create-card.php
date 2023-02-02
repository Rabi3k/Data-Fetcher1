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

?>
<div id='accordion_<?php echo $row->id ?>' class="swiper-slide" tag="<?php echo $row->restaurant_id ?>">
    <div class="cards-wrapper">
        <div class='card opacity-90'>
            <!-- <input type='hidden' id='bgClass_<?php echo $row->id ?>' value='<?php echo $bgClass ?>'/> -->

            <!-- <input type='hidden' id='printValue' value='$printValue'/> -->


            <div class='card-header <?php echo $bgClass ?>' aria-expanded='true' aria-controls='collapse_<?php echo $row->id ?>'>
                <input type='hidden' name="OrderDate" id='OrderDate_<?php echo $row->id ?>' value='<?php echo $jDate ?>' />
                <input type='hidden' name="OrderStatus" id='OrderStatus_<?php echo $row->id ?>' value='<?php echo $bgClass ?>' />

                <div class="row">

                    <div class='col-5 '>
                        <span>#<?php echo $row->id ?></span>
                    </div>
                    <div class='col-7 text-right'>
                        <p>Shipment: <?php echo $row->type ?><br />
                            Payment: <?php echo $row->payment ?></p>
                    </div>

                    <div class='col-12'>
                        <span>Name: <?php echo $row->client_first_name . " " . $row->client_last_name; ?></span>
                    </div>
                </div>

            </div>
            <div id='collapse_<?php echo $row->id ?>' class=' card-body bg-white'>
                <ul class='list-group list-group-flush text-dark'>
                    <?php foreach ($row->items as $item) {
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
            <div class="card-footer">
                <div class="row">
                    <div class="col-2">
                        <button class='btn-sm bg-secondary text-light p-2' id='print_<?php echo $row->id ?>' onclick='<?php echo "PrintElem(" . $row->id . ")" ?>'><i class='fa fa-print' aria-hidden='true'></i></button>

                    </div>
                    <div class="col-4">
                        <small class="time-remaining"></small>
                    </div>
                    <div class="col-6 text-right">
                        <small class="">Til: <?php echo $oDate ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>