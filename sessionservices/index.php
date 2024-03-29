<?php

use Src\Controller\GeneralController;

require_once "../bootstrap.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$requestMethod = $_SERVER["REQUEST_METHOD"];

if (!isset($validator) || $validator != true) {
    echo GeneralController::CreateResponserBody("The world is yours!");
}

if (isset($LoggedInUsers) && $LoggedInUsers == true) {
    if (!$userGateway->checkLogin()) {
        echo GeneralController::CreateResponserBody("The world is yours!");
        exit;
    }
}
