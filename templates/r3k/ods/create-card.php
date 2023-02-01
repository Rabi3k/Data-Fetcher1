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
<div id='accordion_<?php echo $row->id ?>' class="carousel-item" tag="<?php echo $row->restaurant_id ?>" >
    <div class="cards-wrapper">
        <div  class='card opacity-90'>
            <!-- <input type='hidden' id='bgClass_<?php echo $row->id ?>' value='<?php echo $bgClass ?>'/> -->

            <!-- <input type='hidden' id='printValue' value='$printValue'/> -->


            <div class='card-header row ' aria-expanded='true' aria-controls='collapse_<?php echo $row->id ?>'>
                <div class='col-1 <?php echo $bgClass ?>'>
                    <input type='hidden' name="OrderDate" id='OrderDate_<?php echo $row->id ?>' value='<?php echo $jDate ?>' />
                </div>
                <div class='col-5'>
                    <span>Id: <?php echo $row->id ?><br />
                        Name: <?php echo $row->client_first_name . " " . $row->client_last_name; ?></span>
                </div>
                <div class='col-6 text-right'>
                    <p>Shipment: <?php echo $row->type ?><br />
                        Payment: <?php echo $row->payment ?></p>
                </div>
                <div class='col-12'></div>
            </div>
            <div id='collapse_<?php echo $row->id ?>' class=' card-body text-center bg-light'>
                <ul class='list-group text-white'>
                    <?php foreach ($row->items as $item) {
                        if ($item->type === "item") { ?>
                            <li class='list-group-item card-text list-group-item-secondary d-flex justify-content-between align-items-start'>
                                <div class='ms-2 me-auto fw-bold'><?php echo $item->name ?></div>

                                <span class='badge bg-secondary text-white rounded-pill'><?php echo $item->quantity ?></span>
                            </li>
                            <div class='container'>

                                <ul class='list-group'>
                                    <?php if ($item->instructions) { ?>

                                        <li class='list-group-item list-group-item-danger d-flex justify-content-between align-items-start'>
                                            <div class='ms-2 me-auto fw-bold'><?php echo $item->instructions ?></div>
                                        </li>
                                    <?php }
                                    foreach ($item->options as $option) { ?>

                                        <li class='list-group-item list-group-item-primary d-flex justify-content-between align-items-start'>
                                            <div class='ms-2 me-auto fw-bold'><?php echo $option->group_name ?></div>
                                            <div class='ms-2 me-auto fw-bold'><?php echo $option->name ?></div>
                                            <span class='badge bg-secondary text-white rounded-pill'><?php $option->quantity ?></span>

                                        </li>
                                    <?php } ?>

                                </ul>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-6">
                        <button class='btn-sm bg-secondary text-light p-2' id='print_<?php echo $row->id ?>' onclick='<?php echo "PrintElem(" . $row->id . ")" ?>'><i class='fa fa-print' aria-hidden='true'></i></button>
                    </div>
                    <div class="col-6 text-right">
                        <small class="">Til: <?php echo $oDate ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>