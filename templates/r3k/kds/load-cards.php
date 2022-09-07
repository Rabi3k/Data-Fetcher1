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
    
    $pvalue = (object) [
        'title' => $row["id"],
        'values' => [
            "<p>Id: ".$row["id"]."<br/>
        Name: ".$row["client_first_name"]." ".$row["client_last_name"]."</p>",
        ]
    ];
    $printValue = json_encode($pvalue);
echo
"
<div id='accordion_".$row["id"]."' class='card opacity-90 $bgClass'>
    <input type='hidden' id='bgClass' value='$bgClass'/>
    <input type='hidden' id='OrderDate' value='$jDate'/>
    <input type='hidden' id='printValue' value='$printValue'/>
    <span class='btPrint bg-secondary text-light' id='print_".$row["id"]."'><i class='fa fa-print' aria-hidden='true'></i></span>

    <div class='card-header row ' data-toggle='collapse' data-target='#collapse_".$row["id"]."' aria-expanded='true' aria-controls='collapseOne'>
            <div class='col-6'>
                <p>Id: ".$row["id"]."<br/>
                Name: ".$row["client_first_name"]." ".$row["client_last_name"]."</p>
            </div>
            <div class='col-6 text-right'>
                <p>Shipment: ".$row["type"]."<br/>
                Payment: ".$row["payment"]."</p>
            </div>
            <div class='col-12'>Til: $oDate</div>
    </div>
    <div id='collapse_".$row["id"]."' class='collapse card-body text-center' aria-labelledby='headingOne' data-parent='#accordion_".$row["id"]."'>
        <ul class='list-group text-white'>";
        foreach($row["items"] as $item)
        {
            if($item["type"] === "item")
            {
                echo "
                <li class='list-group-item card-text list-group-item-secondary d-flex justify-content-between align-items-start'>
                    <div class='ms-2 me-auto fw-bold'>".$item["name"]."</div>
                    
                    <span class='badge bg-secondary text-white rounded-pill'>".$item["quantity"]."</span>
                </li>
                <div class='container'>
                
                    <ul class='list-group'>";
                    if($item["instructions"])
                    {
                        echo  "
                        <li class='list-group-item list-group-item-danger d-flex justify-content-between align-items-start'>
                            <div class='ms-2 me-auto fw-bold'>".$item["instructions"]."</div>
                        </li>";
                    }
                    foreach($item["options"] as $option)
                    {
                        // Group: ".$option["group_name"]." <br/> Value: ".$option["name"]." <br/> quantity: ".$option["quantity"]."
                        echo "
                        <li class='list-group-item list-group-item-primary d-flex justify-content-between align-items-start'>
                            <div class='ms-2 me-auto fw-bold'>".$option["group_name"]."</div>
                            <div class='ms-2 me-auto fw-bold'>".$option["name"]."</div>
                            <span class='badge bg-secondary text-white rounded-pill'>".$option["quantity"]."</span>
                           
                        </li>";
                    }

                echo "</ul></div>";
            }
        }
        echo "</ul>";
        echo "</div>
</div>";
}
?>