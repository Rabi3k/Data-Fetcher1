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
<!-- <center class="bg-primary opacity-70 text-center text-light">
    Order Panel
</center> -->


<!-- <div class="card-columns" id="orderCards">



    <?php
    //foreach ($data as $row) {
    //include "create-card.php";
    //} 
    ?>
</div> -->
<style>
    /* .card {
        min-width: 30em;
        margin-right: 5px;
    }

    .overflow-div-x {
        overflow-x: auto !important;
        height: -webkit-fill-available;
    }
*/
    .h-90 {
        height: 90vh;
    }

    .carousel-inner {
        padding: 1em;
    }

    .card {
        margin: 0 0.5em;
        box-shadow: 2px 6px 8px 0 rgba(22, 22, 26, 0.18);
        border: none;
    }

    .btn-next,
    .btn-prev {
        width: 5vh;
        height: 5vh;
        border-radius: 50%;
        z-index: 0;
    }

    .controls-container {
        display: flex;
        position: absolute;
        top: 85vh;

        left: 48vw
    }

    @media (min-width: 1260px) {
        .carousel-item {
            margin-right: 0;
            flex: 0 0 33.333333%;
            display: block;
        }

        .carousel-inner {
            display: flex;
        }
    }

    @media (max-width: 1260px) {
        .carousel-item {
            margin-right: 0;
            flex: 0 0 50%;
            display: block;
        }

        .carousel-inner {
            display: flex;
        }

        .controls-container {
            top: 80vh;
        }
    }

    @media (max-width: 768px) {
        .carousel-item {
            margin-right: 0;
            flex: 0 0 100%;
            display: block;
        }

        .carousel-inner {
            display: flex;
        }
        .controls-container {
            top: 75vh;
        }
    }
</style>
<!-- <div class="container-fluid py-2 h-90">
    <div class="d-flex flex-row flex-nowrap overflow-div-x" id="orderCards"> -->
<div id="carouselExampleInterval" class="carousel slide h-90" data-ride="carousel">
    <div class="carousel-inner">
        <?php
        foreach ($data as $key => $row) { ?>
            <div class="carousel-item" data-interval="<?php echo $key ?>">
                <?php include "create-card.php"; ?>
            </div>
        <?php } ?>
    </div>
    <div class="controls-container"> <a class="btn-prev" href="#carouselExampleInterval" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="btn-next" href="#carouselExampleInterval" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

</div>

<script>
    $(document).ready(function() {
        $(".btn-branch").click(function() {
            var tag = $(this).attr("tag");
            if ($(this).attr("aria-pressed") === 'false') {
                //show cards
                $("div[tag=" + tag + "]").show()
            } else {
                //hide cards
                $("div[tag=" + tag + "]").hide()
            }
        });

    });
    var multipleCardCarousel = document.querySelector(
        "#carouselExampleInterval"
    );


    var carouselWidth = $(".carousel-inner")[0].scrollWidth;
    var cardWidth = $(".carousel-item").width();
    var scrollPosition = 0;
    $("#carouselExampleInterval .btn-next").on("click", function() {
        if (scrollPosition < carouselWidth - cardWidth * 4) {
            scrollPosition += cardWidth;
            $("#carouselExampleInterval .carousel-inner").animate({
                    scrollLeft: scrollPosition
                },
                600
            );
        }
    });
    $("#carouselExampleInterval .btn-prev").on("click", function() {
        if (scrollPosition > 0) {
            scrollPosition -= cardWidth;
            $("#carouselExampleInterval .carousel-inner").animate({
                    scrollLeft: scrollPosition
                },
                600
            );
        }
    });
</script>