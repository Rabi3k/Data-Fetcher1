<?php

use Src\Controller\GeneralController;
use Src\TableGateways\IntegrationGateway;

$validator = true;
include "index.php";

if (isset($_GET['q']) && $_GET['q'] != null) {
    $q = strtolower($_GET['q']);
    switch ($q) {
        case 'postcategory':
            # code...
            processRequest();
            break;

        default:
            # code...
            break;
    }
}
function getFunction()
{

}
function processRequest()
{
    global $requestMethod;

    switch ($requestMethod) {
        case 'GET':
            break;
        case 'POST':
            
            $body = file_get_contents('php://input');
            //echo $body;
            $input = json_decode($body);
            //echo json_encode($input);
            PostCategory($input);
            break;
        case 'PUT':
        case 'DELETE':
        default:
            break;
    }
}


function PostCategory($Cat)
{
    global $dbConnection;
    $integrationGateway = new IntegrationGateway($dbConnection);
    $integration = $integrationGateway->findById($Cat->integration_id);
    $curl = curl_init();
    $postFieldId = isset($Cat->l_id) && $Cat->l_id != null ? '"id": "' . $Cat->l_id . '", ' : "";
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.loyverse.com/v1.0/categories',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{' . $postFieldId . ' "name": "' . $Cat->name . '","color": "BLUE"}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            "Authorization: Bearer $integration->LoyverseToken"
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $respCat = $integrationGateway->InsertOrUpdatePostedType($Cat->name, $Cat->gf_id, "category", $integration->Id, $Cat->gf_menu_id, json_decode($response)->id);
    echo GeneralController::CreateResponserBody($respCat);
}
