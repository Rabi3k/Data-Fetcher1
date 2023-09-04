<?php

use Src\Controller\GeneralController;

$validator = true;
include "index.php";

$uid = $_GET["uid"] ?? null;
$token = $_GET["lt"] ?? null;
if (isset($uid)) {
    $uid = urldecode($uid);
    $check = checkGfUid($uid);
    echo GeneralController::CreateResponserBody(array("Uid" => $uid, "Result" => $check, "Error" => $errorMsg));
} else if (isset($token)) {
    $token = urldecode($token);
    $check = checkLoyverseToken($token);
    echo GeneralController::CreateResponserBody(array("Token" => $token, "Result" => $check, "Error" => $errorMsg));
} else {
    echo GeneralController::CreateResponserBody("restaurant Uid is missing!");
}
/* 685a16950383408ca5dd9f883f291d5e */
$errorMsg = null;
function checkLoyverseToken(string $token): array
{
    global $errorMsg;
    $errorMsg = null;
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.loyverse.com/v1.0/stores',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $token"
        ),
    ));

    $response = curl_exec($curl);
    $responseObj = json_decode($response);
    curl_close($curl);
    if (isset($responseObj->errors)) {
        //echo $response;
        $errorMsg = $responseObj->errors;
        return array("valid" => false, "errorMsg" => $errorMsg);
    }
    return array("valid" => true, "StoreId" => $responseObj->stores[0]->id);
}
function checkGfUid(string $UID): bool
{
    global $errorMsg;
    $errorMsg = null;

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.restaurantlogin.com/api/restaurant/$UID/menu?active=true&pictures=false",
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
    curl_close($curl);


    if (isset($responseObj->errorDescription)) {
        //echo $response;
        $errorMsg = $responseObj->errorDescription;
        return false;
    }
    return true;
}
