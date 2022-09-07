<!DOCTYPE html>
<?php
use Src\TableGateways\RequestsGateway;
$requestGateway = new RequestsGateway($dbConnection);

$sDate =(new \DateTime('today midnight',new \DateTimeZone('Europe/Copenhagen')));
$eDate =(new \DateTime('tomorrow midnight',new \DateTimeZone('Europe/Copenhagen')));


$data = $requestGateway->RetriveAllOrdersByDate($sDate,$eDate);
foreach($data as $row)
{
    $OrderDate = new \DateTime($row["fulfill_at"]??$row["updated_at"]);
    $OrderDate->setTimezone( new \DateTimeZone($row["restaurant_timezone"]));
    $jDate=$OrderDate->format('Y/m/d h:i:s');
    $oDate= $OrderDate->format('l-M-y  h:i:s');

    
    $todayW = (new \DateTime())->setTimestamp(strtotime("-10 minutes"));
    $today = new \DateTime();
    $bgClass='bg-info';
    if($oDate>$todayW)
    {
        $bgClass='bg-info';
    }
    elseif($oDate<$today)
    {
        $bgClass='bg-danger';
    }
    elseif($oDate<$todayW)
    {
        $bgClass='bg-warning';
    }
?>
<div id='accordion_<?php echo $row["id"] ?>' class='card opacity-90 <?php echo $bgClass ?>'>
    <input type='hidden' id='bgClass' value='<?php echo $bgClass ?>'/>
    <!-- <input type='hidden' id='OrderDate' value='$jDate'/> -->
    <!-- <input type='hidden' id='printValue' value='$printValue'/> -->
    <button class='btPrint bg-secondary text-light' id='print_<?php echo $row["id"] ?>' onclick='<?php echo "PrintElem(".$row["id"].")" ?>'><i class='fa fa-print' aria-hidden='true'></i></button>

    <div class='card-header row ' data-toggle='' data-target='#collapse_<?php echo $row["id"] ?>' aria-expanded='true' aria-controls='collapse_<?php echo $row["id"] ?>'>
            <div class='col-6'>
                <span>Id: <?php echo $row["id"] ?><br/>
                Name: <?php echo $row["client_first_name"]." ".$row["client_last_name"]; ?></span>
            </div>
            <div class='col-6 text-right'>
                <p>Shipment: <?php echo $row["type"] ?><br/>
                Payment: <?php echo $row["payment"] ?></p>
            </div>
            <div class='col-12'>Til: <?php echo $oDate ?></div>
    </div>
    <div id='collapse_<?php echo $row["id"] ?>' class=' card-body text-center' aria-labelledby='headingOne' data-parent='#accordion_<?php echo $row["id"] ?>'>
        <ul class='list-group text-white'>
        <?php foreach($row["items"] as $item){ 
            if($item["type"] === "item")
            { ?>
                <li class='list-group-item card-text list-group-item-secondary d-flex justify-content-between align-items-start'>
                    <div class='ms-2 me-auto fw-bold'><?php echo $item["name"] ?></div>
                    
                    <span class='badge bg-secondary text-white rounded-pill'><?php echo $item["quantity"] ?></span>
                </li>
                <div class='container'>
                
                    <ul class='list-group'>
                    <?php if($item["instructions"])
                    { ?>
                       
                        <li class='list-group-item list-group-item-danger d-flex justify-content-between align-items-start'>
                            <div class='ms-2 me-auto fw-bold'><?php echo $item["instructions"] ?></div>
                        </li>";
                <?php } 
                    foreach($item["options"] as $option)
                    {?>
                        
                        <li class='list-group-item list-group-item-primary d-flex justify-content-between align-items-start'>
                            <div class='ms-2 me-auto fw-bold'><?php echo $option["group_name"] ?></div>
                            <div class='ms-2 me-auto fw-bold'><?php echo $option["name"] ?></div>
                            <span class='badge bg-secondary text-white rounded-pill'>".$option["quantity"]."</span>
                           
                        </li>";
                <?php } ?>

                </ul></div>
                <?php } ?>
            <?php } ?>
      </ul>
    </div>
</div>
<?php } ?>