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
            // echo json_encode($item);
            $l_ids = PostItem($item);
            // http_response_code(200);
            // echo GeneralController::CreateResponserBody($l_ids);

            $integrationGateway = new IntegrationGateway($dbConnection);
            if (count($item->sizes) > 0) {
                $respItem = $integrationGateway->InsertOrUpdatePostedType($item->name, $item->id, "item", $item->integration_id, $item->gf_menu_id, $l_ids->l_id);
                $variants = array();
                foreach ($item->sizes as $key => $value) {
                    # code... PostOptions
                    $variants[] = array(
                        "gf_id" => $value->id,
                        "name" => $value->name,
                        "l_id" => $l_ids->ol_id[$value->id] ?? $l_ids->ol_id[$key],
                    );
                }
                $respOptions = $integrationGateway->InsertOrUpdateBatchPostedType("variant",  $item->integration_id, $item->gf_menu_id, $variants, $item->id, $l_ids->l_id);
                $respItem->variants = $respOptions;
            } else {
                $respItem = $integrationGateway->InsertOrUpdatePostedType($item->name, $item->id, "item", $item->integration_id, $item->gf_menu_id, $l_ids->l_id, NULL, $l_ids->ol_id[0]);
            }
            echo GeneralController::CreateResponserBody($respItem);
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
            $respOptions = $integrationGateway->InsertOrUpdateBatchPostedType("option", $modifier->integration_id, $modifier->gf_menu_id, $opts, $modifier->gf_id, $l_ids->l_id);
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

function PostItem($item)
{
    global $dbConnection;
    $integrationGateway = new IntegrationGateway($dbConnection);
    $integration = $integrationGateway->findById($item->integration_id);
    $cat = $integrationGateway->GetTypeByIntegrationAndGfId($item->gf_category_id, $item->integration_id, "category");

    //echo json_encode($cat);

    $curl = curl_init();
    $mo = array();
    $si = array();
    foreach ($item->sizes as $key => $s) {
        # code...
        $si[] = (object)array(
            "id" => (isset($s->loyverse_id) && $s->loyverse_id != null ? $s->loyverse_id : null),
            //"name" => $s->name,
            "default_price" => ($s->price) + ($item->price),
            "reference_variant_id" => $s->id,
            "default_pricing_type" => "FIXED",
            "option1_value" => $s->name,
            "option2_value" => null,
            "option3_value" => null,
            //"stores"=> array("$integration->StoreId"),
        );
        foreach ($s->groups as $key => $g) {
            # code...
            if (!in_array($g->loyverse_id, $mo)) {
                $mo[] = $g->loyverse_id;
            }
        }

        //'{'. $opostFieldId .'"name": "'. $value->name .'","price": '. $value->price .'}';
    }
    foreach ($item->groups as $key => $g) {
        # code...
        if (!in_array($g->loyverse_id, $mo)) {
            $mo[] = $g->loyverse_id;
        }
    }
    $datas = (object)array(
        "id" => (isset($item->loyverse_id) && $item->loyverse_id != null ? $item->loyverse_id : null),
        "item_name" => $item->name,
        "reference_id" => $item->id,
        "description" => $item->description,
        "category_id" => $cat->loyverse_id,
        "is_composite" => false,
        "option1_name" =>  count($si) > 0 ? "Size" : null,
        "option2_name" => null,
        "option3_name" => null,
        "sold_by_weight" => false,
        "track_stock" => false,
        "use_production" => false,
        "tax_ids" => array("$integration->TaxId"),
        "variants" => count($si) > 0 ? $si : array((object)array(
            "default_pricing_type" => "FIXED",
            "default_price" => $item->price,
        )),
        "modifier_ids" => $mo,

    );

    $data = json_encode($datas);
    //echo $data;
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.loyverse.com/v1.0/items',
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
     if (!isset($resp->id)) {
         unprocessableEntityResponse($resp);
         exit;
     }
    if (isset($item->picture_hi_res) && $item->picture_hi_res != "") {
        $contents = file_get_contents($item->picture_hi_res);
       
        //echo $contents;
        $curl1 = curl_init();

        curl_setopt_array($curl1, array(
            CURLOPT_URL => "https://api.loyverse.com/v1.0/items/$resp->id/image",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $contents,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: image/png',
                "Authorization: Bearer $integration->LoyverseToken"
            ),
        ));

        $response2 = curl_exec($curl1);
        echo $response2;
        //echo(json_encode(curl_getinfo($curl1)));

        curl_close($curl1);

        
    }
   
    $optIds = array(); //array_column($resp->variants, "variant_id");
    foreach ($resp->variants as $key => $value) {
        if (isset($value->reference_variant_id) && !is_null($value->reference_variant_id) && $value->reference_variant_id != "") {
            $optIds[$value->reference_variant_id] = $value->variant_id;
        } else {
            $optIds[] = $value->variant_id;
        }

        # code...
    }

    $m_id = $resp->id;
    return (object)array(
        "l_id" => $m_id,
        "ol_id" => $optIds
    );
}

function PostModifier($modifier)
{
    global $dbConnection;
    $integrationGateway = new IntegrationGateway($dbConnection);
    $integration = $integrationGateway->findById($modifier->integration_id);

    $curl = curl_init();
    $mo = array();
    foreach ($modifier->options as $key => $value) {
        # code...
        $opostFieldId = isset($modifier->l_id) && $modifier->l_id != null ? '"id": "' . $modifier->l_id . '", ' : "";
        $mo[] = (object)array(
            "id" => (isset($value->l_id) && $value->l_id != null ? $value->l_id : null),
            "name" => $value->name,
            "price" => $value->price
        );
        //'{'. $opostFieldId .'"name": "'. $value->name .'","price": '. $value->price .'}';
    }
    $datas = (object)array(
        "id" => (isset($modifier->l_id) && $modifier->l_id != null ? $modifier->l_id : null),
        "name" => $modifier->name,
        "stores" => array("$integration->StoreId"),
        "modifier_options" => $mo
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
    if (!isset($resp->id)) {
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
    $resp = json_decode($response);
    curl_close($curl);
    if (!isset($resp->id)) {
        unprocessableEntityResponse($resp);
        exit;
    }
    return $resp->id;
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
