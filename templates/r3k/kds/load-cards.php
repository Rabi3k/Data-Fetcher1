<!DOCTYPE html>
<?php
use Src\TableGateways\RequestsGateway;
$requestGateway = new RequestsGateway($dbConnection);

$sDate =(new \DateTime('today midnight',new \DateTimeZone('Europe/Copenhagen')));
$eDate =(new \DateTime('tomorrow midnight',new \DateTimeZone('Europe/Copenhagen')));

$secrets = $userLogin->GetUser()?->secrets??array();
$data = $requestGateway->RetriveAllOrdersByDate($sDate,$eDate,$secrets);
$idOrders = array_column($data, 'id');
//echo "<span class='card'>".json_encode($data)." Test</span><br/>";
?>
<script>
    ActiveOrderIds =<?php echo json_encode($idOrders) ?>;
</script>
<?php
foreach($data as $row)
{
    include "create-card.php";
}