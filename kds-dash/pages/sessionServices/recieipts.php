<?php

use Src\TableGateways\IntegrationGateway;

$startDate = $_GET["s"] ?? NULL;
$endDate = $_GET["e"] ?? null;
$integrationId =$_GET["i"] ?? null;
$integrationGateway = new IntegrationGateway($dbConnection);
$integration = $integrationGateway->findById($integrationId);
    $UTC = new DateTimeZone("UTC");

if ($sDate = \DateTime::createFromFormat('Ymd', ($startDate), new \DateTimeZone('Europe/Copenhagen'))) {
    $sDate->setTime(0, 0);
    $sDate->setTimezone( $UTC );

}
if ($eDate = \DateTime::createFromFormat('Ymd', ($endDate), new \DateTimeZone('Europe/Copenhagen'))) {
    $eDate->setTime(23, 59, 59, 999999);
    $eDate->setTimezone( $UTC );

}
$receipts = GetReceipts($sDate, $eDate);

function GetReceipts(DateTime $startDate, DateTime $endDate)
{
    $cursor = null;
    $receipts = [];
    do {
        $resp = FetchReceipts($startDate, $endDate, $cursor);
        $receipts = array_merge($receipts, $resp["receipts"]);
        $cursor = $resp["cursor"];
        # code...
    } while ($cursor != null);




    //echo $response;
    return $receipts;
}
function FetchReceipts(DateTime $startDate, DateTime $endDate, string $cursor = null)
{
    global $integration;
    $sdate = $startDate->format("Y-m-d\TH:i:s.000\Z");
    $edate = $endDate->format("Y-m-d\TH:i:s.999\Z");
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.loyverse.com/v1.0/receipts?updated_at_min=$sdate&updated_at_max=$edate&limit=250&cursor=$cursor",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $integration->LoyverseToken"
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $resp = json_decode($response);

    //echo $response;
    return array("receipts" => $resp->receipts, "cursor" => $resp->cursor);
}


$retval = array(
    "e" => $endDate,
    "s" => $startDate,
    "data" => $receipts
);
echo json_encode($retval);
