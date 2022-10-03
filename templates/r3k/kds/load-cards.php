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
    ActiveOrderIds =<?php echo json_encode($idOrders) ?>;
</script>
<?php $allBranches = $userLogin->GetUser()->UserBranches();
if(count($allBranches)>1) {
?>
<ul class="nav nav-pills nav-fill bg-dark text-light">
    <?php foreach ($allBranches as $key => $value) {?>
        <li class="nav-item">
    <a class="nav-link" href="#sec"><?php echo "$value->city, $value->address<br/>"; ?></a>
  </li>
    <?php } ?>
</ul>
<?php } ?>
<div class="card-columns" id="orderCards">



<?php
    foreach ($data as $row) {
        include "create-card.php";
    } ?>
</div>