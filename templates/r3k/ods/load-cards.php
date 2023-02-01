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
    //} ?>
</div> -->
<style>
.card {
    min-width: 30em;
    margin-right: 5px;
}
.overflow-div-x
{
overflow-x: auto !important;
height: -webkit-fill-available;
}
.h-90 {
    height: 90vh;
}
</style>
<div class="container-fluid py-2 h-90">
    <div class="d-flex flex-row flex-nowrap overflow-div-x" id="orderCards">
    <?php
    foreach ($data as $row) {
        include "create-card.php";
    } ?>
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
        })
    })
</script>