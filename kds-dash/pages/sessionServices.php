<?php


use Src\Controller\RequestController;
use Src\Controller\OrderController;
use Src\Controller\ActiveOrderController;
use Src\Controller\GeneralController;
use Src\Controller\IntegrationController;
use Src\Controller\OrderItemController;
use Src\Controller\UsersController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
if (strtolower($_GET["q"]) == "receipts") {
    include "sessionServices/recieipts.php";
} else {
    $retval = array(
        "data" => $_GET["q"],
        "e" => $_GET["e"],
        "s" => $_GET["s"],
    );
    echo json_encode($retval);
}
