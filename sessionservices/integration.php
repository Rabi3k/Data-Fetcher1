<?php

use React\Http\Message\Response;
use Src\Controller\GeneralController;
use Src\TableGateways\IntegrationGateway;

$validator = true;
include "index.php";

if (isset($_GET['q']) && $_GET['q'] != null) {
    $q = strtolower($_GET['q']);
    switch ($q) {
        case 'postcategory':
            # code...
            categoryProcessRequest();
            break;
        case 'postcategories':
            # code...
            categoriesProcessRequest();
            break;

        default:
            # code...
            break;
    }
}
function getFunction()
{
}
function categoryProcessRequest()
{
    global $dbConnection, $requestMethod;

    switch ($requestMethod) {
        case 'GET':
            break;
        case 'POST':

            $body = file_get_contents('php://input');
            //echo $body;
            $Cat = json_decode($body);
            //echo json_encode($input);
            $Cat->l_id = PostCategory($Cat);
            $integrationGateway = new IntegrationGateway($dbConnection);
            $respCat = $integrationGateway->InsertOrUpdatePostedType($Cat->name, $Cat->gf_id, "category", $Cat->integration_id, $Cat->gf_menu_id, $Cat->l_id);
            echo GeneralController::CreateResponserBody($respCat);
            break;
        case 'PUT':
        case 'DELETE':
        default:
            break;
    }
}

function categoriesProcessRequest()
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
            $respCats = PostCategories($input);
            echo GeneralController::CreateResponserBody($respCats);
            break;
        case 'PUT':
        case 'DELETE':
        default:
            break;
    }
}


function PostCategories($Cats)
{
    //echo json_encode($Cats);
    global $dbConnection;
    $integrationGateway = new IntegrationGateway($dbConnection);
    // $integration = $integrationGateway->findById($Cats->integration_id);
    /*
    {"integration_id":"1","gf_menu_id":"433279","gf_id":"2628705","l_id":"4925a8cf-3645-428e-8c01-aeb181a99ca6","name":"Pizza"}
     */
    $cni = array();

    foreach ($Cats->categories as $key => $cat) {
        $Catsi = new stdClass();
        $cat->integration_id=$Cats->integration_id;
        $cat->gf_menu_id=$Cats->gf_menu_id;
        $cat->l_id = PostCategory($cat);
        $cni[] = array(
            "categoryName" => $cat->name,
            "loyverse_id" => $cat->l_id,
            "gf_id" => $cat->gf_id,
        );
    }
   return $integrationGateway->InsertOrUpdateBatchPostedType("category", $Cats->integration_id, $Cats->gf_menu_id, $cni);
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
        CURLOPT_POSTFIELDS => '{' . $postFieldId . ' "name": "' . $Cat->name . '","color": "RED"}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            "Authorization: Bearer $integration->LoyverseToken"
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response)->id;
}
