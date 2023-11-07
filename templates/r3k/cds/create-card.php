<?php
session_start();

use Src\TableGateways\OrdersGateway;
use Src\Classes\Order;
use Src\TableGateways\UserGateway;

$ordersGateway = new OrdersGateway($dbConnection);

$sDate = (new \DateTime('today midnight', new \DateTimeZone('Europe/Copenhagen')));
$eDate = (new \DateTime('tomorrow midnight', new \DateTimeZone('Europe/Copenhagen')));

$secrets = $userGateway->GetUser()?->secrets ?? array();
$data = $ordersGateway->FindInHouseActiveByDate($sDate, $eDate, UserGateway::$user->Restaurants_Id);
//var_dump(UserGateway::$user->Restaurants_Id);
$now = (new \DateTime('now', new \DateTimeZone('Europe/Copenhagen')));


$retval = array();
foreach ($data as $order) {
    // echo ($order->getJsonStr());
    $OrderDate = new \DateTime($order->fulfill_at ?? $order->updated_at);
    $OrderDate->setTimezone(new \DateTimeZone($order->restaurant_timezone));

    $timeToEnd = $now->diff($OrderDate);
    
//echo $timeToEnd->format("%H:%I:%S");

    $timeToEndTxt = "";
    if ($order->is_done) {
        $timeToEndTxt = "Nu";
    } else {
            $timeToEndTxt = $timeToEnd->format("%H:%I:%S");
        if ($timeToEnd->invert > 0) { {
                $timeToEndTxt = "Om et øjeblik";
            }
        }
        
    }
    switch ($order->type) {
        case 'pickup':
            $orderTypeText = 'Afhentning';
            break;
        case 'delivery':
            $orderTypeText = 'Levering';
            break;
        case 'order_ahead':
            $orderTypeText = 'Bordreservation';
            break;
        case 'table_reservation':
            $orderTypeText = 'Bordreservation';
            break;
        case 'dine_in':
            $orderTypeText = 'Spise i';
            break;
        default:
            break;
    }

    $retval[] = array(
        "id" => $order->id,
        "client_name" => $order->client_first_name . " " . $order->client_last_name,
        "table_number" => $order->table_number,
        "fulfill_at" => $OrderDate->format("Y-m-d h:i:s a"),
        "type" => $orderTypeText,
        "timeToEnd" => $timeToEndTxt,
    );
}

// if you have more session-vars that are needed for login, also check 
// if they are set and refresh them as well
if (isset($_SESSION['UserId'])) { 
  $_SESSION['UserId'] = $_SESSION['UserId'];

}

echo '{"draw": 1,
    "recordsTotal":' . count($data) . ',
    "recordsFiltered": ' . count($data) . ',
    "data":' . json_encode($retval) . "}";
