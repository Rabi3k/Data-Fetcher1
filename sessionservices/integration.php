<?php

use PhpParser\Node\Expr\Cast\Object_;
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
        case 'postmodifier':
            # code...
            modifierProcessRequest();
            break;
        case 'postitem':
            # code...
            itemProcessRequest();
            break;

        default:
            # code...
            break;
    }
}

function itemProcessRequest()
{
    global $dbConnection, $requestMethod;

    switch ($requestMethod) {
        case 'GET':
            break;
        case 'POST':

            $body = file_get_contents('php://input');
            //echo $body;
            $item = json_decode($body);
            echo json_encode($item);
            // $l_ids = PostModifier($modifier);
            // $integrationGateway = new IntegrationGateway($dbConnection);
            // $respModifier = $integrationGateway->InsertOrUpdatePostedType($modifier->name, $modifier->gf_id, "item", $modifier->integration_id, $modifier->gf_menu_id, $l_ids->l_id);
            // $opts = array();
            // foreach ($modifier->options as $key => $value) {
            //     # code... PostOptions
            //     $opts[] = array(
            //         "gf_id" => $value->gf_id,
            //         "name" => $value->name,
            //         "l_id" => $l_ids->ol_id[$key],
            //     );
            // }
            // $respOptions = $integrationGateway->InsertOrUpdateBatchPostedType("option", $modifier->integration_id, $modifier->gf_menu_id, $opts);
            // $respModifier->options = $respOptions;
            // echo GeneralController::CreateResponserBody($respModifier);
            break;
        case 'PUT':
        case 'DELETE':
        default:
            break;
    }
}

function modifierProcessRequest()
{
    global $dbConnection, $requestMethod;

    switch ($requestMethod) {
        case 'GET':
            break;
        case 'POST':

            $body = file_get_contents('php://input');
            //echo $body;
            $modifier = json_decode($body);
            //echo json_encode($modifier);
            $l_ids = PostModifier($modifier);
            $integrationGateway = new IntegrationGateway($dbConnection);
            $respModifier = $integrationGateway->InsertOrUpdatePostedType($modifier->name, $modifier->gf_id, "modifier", $modifier->integration_id, $modifier->gf_menu_id, $l_ids->l_id);
            $opts = array();
            foreach ($modifier->options as $key => $value) {
                # code... PostOptions
                $opts[] = array(
                    "gf_id" => $value->gf_id,
                    "name" => $value->name,
                    "l_id" => $l_ids->ol_id[$key],
                );
            }
            $respOptions = $integrationGateway->InsertOrUpdateBatchPostedType("option", $modifier->integration_id, $modifier->gf_menu_id, $opts);
            $respModifier->options = $respOptions;
            echo GeneralController::CreateResponserBody($respModifier);
            break;
        case 'PUT':
        case 'DELETE':
        default:
            break;
    }
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

function PostModifier($modifier)
{
    global $dbConnection;
    $integrationGateway = new IntegrationGateway($dbConnection);
    $integration = $integrationGateway->findById($modifier->integration_id);

    $curl = curl_init();
    $postFieldId = isset($modifier->l_id) && $modifier->l_id != null ? '"id": "' . $modifier->l_id . '", ' : "";
    $mo = array();
    foreach ($modifier->options as $key => $value) {
        # code...
        $opostFieldId = isset($modifier->l_id) && $modifier->l_id != null ? '"id": "' . $modifier->l_id . '", ' : "";
        $mo[] = (object)array(
            "id" => (isset($value->l_id) && $value->l_id != null ? $value->l_id:null),
            "name" => $value->name,
            "price" =>$value->price
        );
        //'{'. $opostFieldId .'"name": "'. $value->name .'","price": '. $value->price .'}';
    }
    $datas = (object)array(
        "id" => (isset($modifier->l_id) && $modifier->l_id != null ? $modifier->l_id:null),
        "name" => $modifier->name,
        "stores"=> array("$integration->StoreId"),
        "modifier_options" =>$mo
    );
    $data = json_encode($datas);
    //echo $data;
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.loyverse.com/v1.0/modifiers',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            "Authorization: Bearer $integration->LoyverseToken"
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    //echo $response;
    $resp = json_decode($response);
    if(!isset($resp->id))
    {
        unprocessableEntityResponse($resp);
        exit;
    }
    $optIds = array_column($resp->modifier_options, "id");
    $m_id = $resp->id;
    return (object)array(
        "l_id" => $m_id,
        "ol_id" => $optIds
    );
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
function unprocessableEntityResponse($error)
    {
        http_response_code(422);
        $response = json_encode([
            'error' => $error
        ]);

        echo GeneralController::CreateResponserBody(json_decode($response));
       // $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
    }