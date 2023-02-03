<!DOCTYPE html>
<?php

use Src\TableGateways\OrdersGateway;

$ordersGateway = new OrdersGateway($dbConnection);

$sDate = (new \DateTime('today midnight', new \DateTimeZone('Europe/Copenhagen')));
$eDate = (new \DateTime('tomorrow midnight', new \DateTimeZone('Europe/Copenhagen')));

$secrets = $userLogin->GetUser()?->secrets ?? array();
$data = $ordersGateway->FindActiveByDate($sDate, $eDate, $secrets);
$idOrders = array_column($data, 'id');
//echo "<span class='card'>".json_encode($data)." Test</span><br/>";
?>
<script>
    ActiveOrderIds = <?php echo json_encode($idOrders) ?>;
</script>

<div id="main-swiper" class="swiper h-90">
    <div class="swiper-pagination"></div>

    <div class="swiper-wrapper" id="orderCards">
        <?php
        foreach ($data as $key => $row) { ?>
            <?php include "create-card.php"; ?>
        <?php } ?>
    </div>

    <!-- <div class="controls-container"> <a class="btn-prev" href="#carouselExampleInterval" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="btn-next" href="#carouselExampleInterval" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div> -->
</div>
