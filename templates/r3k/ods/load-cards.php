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

<div id="carouselExampleInterval" class="swiper h-90">
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
<script>

    var swiperCar = new Swiper('.swiper', {
        pagination: {
            el: '.swiper-pagination',
        },
        slidesPerGroup: 1,
        spaceBetween: 10,
        slidesPerView: 1,
        slidesPerColumn: 1,
        cssMode: false,


        // Breakpoints
       breakpoints: {
        // when window width is >= 320px
        
        850: {
          slidesPerView: 2,
          spaceBetween: 10
        },
        // when window width is >= 480px
        1296: {
          slidesPerView: 3,
          spaceBetween: 10
        },
        // when window width is >= 640px
        1680: {
          slidesPerView: 4,
          spaceBetween: 10
        }
       },


        // To support iOS's swipe-to-go-back gesture (when being used in-app).
        edgeSwipeDetection: false,
        edgeSwipeThreshold: 20,

        effect: 'slide',
        // Unique Navigation Elements
        uniqueNavElements: true,

        // Resistance
        resistance: true,
        resistanceRatio: 0.85,

         // Round length
        roundLengths:false,

        // Options for touch events
        touchRatio: 1,
        touchAngle: 45,
        simulateTouch: true,
        shortSwipes: true,
        longSwipes: true,
        longSwipesRatio: 0.5,
        longSwipesMs: 300,
        followFinger: true,
        allowTouchMove: true,
        threshold: 0,
        touchMoveStopPropagation: false,
        touchStartPreventDefault: true,
        touchStartForcePreventDefault: false,
        touchReleaseOnEdges: false,

        // Use ResizeObserver (if supported by browser) on swiper container to detect container resize (instead of watching for window resize)
    resizeObserver:true,

    });


   
</script>