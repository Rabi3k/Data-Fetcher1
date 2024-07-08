<?php

use Src\Classes\Integration;
use Src\Controller\GeneralController;
use Src\TableGateways\IntegrationGateway;
use Src\TableGateways\OrdersGateway;

$validator = true;
include "index.php";

$id = $_GET["id"] ?? null;
$iid = $_GET["iid"] ?? null;
$gf_refid = $_GET["refid"] ?? null;
$uid = $_GET["uid"] ?? null;
$token = $_GET["lt"] ?? null;
if(!isset($gf_refid) || $gf_refid ==null || intval($gf_refid) ==0 )
{
    echo GeneralController::CreateResponserBody(array("message" => "Restaurant ref id is missing 'refid'"));
    exit;
}
if(!isset($iid) || $iid ==null || intval($iid) ==0 )
{
    echo GeneralController::CreateResponserBody(array("message" => "integration id is missing 'iid'"));
    exit;
}
$integrationGateway = new IntegrationGateway($dbConnection);
$Orders = (new OrdersGateway($dbConnection))->FindByRestaurantRefId($gf_refid);
$results = array();
$pOrders = $integrationGateway->GetBatchTypeByIntegrationAndType($iid, 'order');
foreach ($Orders as $key => $order) {
    $order->loyverse_id = isset($pOrders[$order->id]) ? $pOrders[$order->id]->loyverse_id : null;
    $fmt = new NumberFormatter('da_DK', NumberFormatter::CURRENCY);

    $amount = $fmt->formatCurrency($order->total_price ?? 0, $order->currency);
    $deliveryFee = $fmt->formatCurrency($order->deliveryFee ?? 0, $order->currency);
    $promoItemValues = $fmt->formatCurrency($order->promoItemValues ?? 0, $order->currency);
    $promoCartValues = $fmt->formatCurrency($order->promoCartValues ?? 0, $order->currency);

    $validationClass =  isset($order->hasIssue) && $order->hasIssue != false ? "has-issue" : (isset($order->loyverse_id) && $order->loyverse_id != null ? 'is-valid'  : "is-invalid");

    $retOrder = new stdClass();
    
    $retOrder->id = $order->id;
    $retOrder->name = "$order->client_first_name $order->client_last_name";
    $retOrder->date = $order->fulfill_at;
    $retOrder->time = $order->fulfill_at;
    $retOrder->type = $order->type;
    $retOrder->payment = $order->payment;
    $retOrder->currency =$order->currency;
    $retOrder->total_price_a = intval($order->total_price);
    $retOrder->total_price = $amount;
    $retOrder->deliveryFee_a = intval($order->deliveryFee);
    $retOrder->deliveryFee = $deliveryFee;
    $retOrder->promoCartValues_a = intval($order->promoCartValues);
    $retOrder->promoCartValues = $promoCartValues;
    $retOrder->promoItemValues_a = intval($order->promoItemValues);
    $retOrder->promoItemValues = $promoItemValues;
    $retOrder->validationClass = $validationClass;
    $retOrder->loyverse_id = $order->loyverse_id ?? null;
    $retOrder->order = $order->getJson();
    $results[]=$retOrder;
}
echo GeneralController::CreateResponserBody($results);