<?php

use Src\TableGateways\IntegrationGateway;
$validator = true;
include "index.php";
$uid = isset($_GET["uid"]) && $_GET["uid"] != null && $_GET["uid"] != "" ? $_GET["uid"] : null;
$integration_Id = isset($_GET["iid"]) && $_GET["iid"] != null && $_GET["iid"] != "" ? $_GET["iid"] : null;
$liArr =array();
if ($uid != null && $integration_Id!=null) {
    $liArr = GetPromotion($uid,$integration_Id);
}

function GetPromotion($UID,$integration_Id)
{
    global $dbConnection;

    $integrationGateway = new IntegrationGateway($dbConnection);
    $postedDiscounts = $integrationGateway->GetBatchTypeByIntegrationAndType($integration_Id,'discount');

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.restaurantlogin.com/api/restaurant/$UID/promotions",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    $responseObj = json_decode($response);
    foreach ($responseObj as $key => $value) {
        $value->loyverseId=null;
        if(isset($postedDiscounts) && count($postedDiscounts)>0 && array_key_exists($value->id,$postedDiscounts))
        {
            $value->loyverseId=$postedDiscounts[$value->id]->loyverse_id;
        }
        # code...
    }
    curl_close($curl);
    return $responseObj;
}


echo '{"draw": 1,
    "recordsTotal":' . count($liArr) . ',
    "recordsFiltered": ' . count($liArr) . ',
    "data":' . json_encode($liArr) . "}";
