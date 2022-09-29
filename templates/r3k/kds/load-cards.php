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
<div class="card-columns" id="orderCards">

<nav class="nav flex-column bg-light">
    <li class="nav-item">
        <a class="nav-link active" href="#">Item 1</a>
    </li>
    <li class="nav-item">
        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Item 2</a>
    </li>
</nav>

<?php
    foreach ($data as $row) {
        include "$templatePath/kds/create-card.php";
    } ?>
</div>