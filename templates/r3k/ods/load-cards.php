<!DOCTYPE html>
<?php

use Src\TableGateways\OrdersGateway;
use Src\TableGateways\UserGateway;

$ordersGateway = new OrdersGateway($dbConnection);

$sDate = (new \DateTime('today midnight', new \DateTimeZone('Europe/Copenhagen')));
$eDate = (new \DateTime('tomorrow midnight', new \DateTimeZone('Europe/Copenhagen')));

$secrets = $userGateway->GetUser()?->secrets ?? array();
//$data = $ordersGateway->FindActiveByDate($sDate, $eDate, $secrets);
$data = $ordersGateway->FindActiveIdsByDateRestaurantRefId($sDate, $eDate, UserGateway::$user->Restaurants_Id);
$idOrders = array_column($data, 'id');
//echo "<span class='card'>".json_encode($data)." Test</span><br/>";
?>
<script>
    ActiveOrderIds = <?php echo json_encode($data) ?>;
</script>

<div id="main-swiper" class="swiper h-80">
    <div class="swiper-pagination"></div>

    <div class="swiper-wrapper" id="orderCards">
        <?php
        foreach ($data as $key => $row) { ?>
            <?php include "create-card.php"; ?>
        <?php } ?>
    </div>
</div>
