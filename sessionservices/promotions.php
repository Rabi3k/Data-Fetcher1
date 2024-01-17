<?php



use Src\Controller\GeneralController;
use Src\Controller\IntegrationController;
use Src\TableGateways\IntegrationGateway;

$validator = true;
include "index.php";

if (isset($_GET['q']) && $_GET['q'] != null) {
    $q = strtolower($_GET['q']);
    switch ($q) {
        case 'gfpromotions':
            # code...
            GfPromotionsProcessRequest();
            break;
        case 'lpromotions':
            # code...
            LoyveresPromotionsProcessRequest();
            break;


        default:
            echo GeneralController::CreateResponserBody("The world is yours!");
            # code...
            break;
    }
}

function GfPromotionsProcessRequest()
{
    global $dbConnection, $requestMethod;

    switch ($requestMethod) {
        case 'GET':
            $uid = isset($_GET["uid"]) && $_GET["uid"] != null && $_GET["uid"] != "" ? $_GET["uid"] : null;
            $integration_Id = isset($_GET["iid"]) && $_GET["iid"] != null && $_GET["iid"] != "" ? $_GET["iid"] : null;
            $liArr = array();
            if ($uid != null && $integration_Id != null) {
                $liArr = GetGFPromotion($uid, $integration_Id);
            }
            $retval = (object)array(
                "draw" => 1,
                "recordsTotal" => count($liArr),
                "recordsFiltered" => count($liArr),
                "data" => ($liArr)
            );
            echo json_encode($retval);

            break;

        case 'POST':
        case 'PUT':
        case 'DELETE':
        default:
            break;
    }
}

function LoyveresPromotionsProcessRequest()
{
    global $dbConnection, $requestMethod;

    switch ($requestMethod) {
        case 'GET':

            $integration_Id = isset($_GET["iid"]) && $_GET["iid"] != null && $_GET["iid"] != "" ? $_GET["iid"] : null;
            $liArr = array();
            if ($integration_Id != null) {
                $liArr = GetLoyveresDiscounts(intval($integration_Id));
            }
            $retval = (object)array(
                "draw" => 1,
                "recordsTotal" => count($liArr),
                "recordsFiltered" => count($liArr),
                "data" => ($liArr)
            );
            echo json_encode($retval);

            break;

        case 'POST':
            $body = file_get_contents('php://input');
            //echo $body;
            /*
            {
                'integrationId':(int),
                'gfMenuId':(int),
                'discountId':(string / GUID),
                'discountName':(string),
            }
             */
            $discountBody = json_decode($body);
            //echo json_encode($discountBody);
            
            $integrationGateway = new IntegrationGateway($dbConnection);
            $integration= $integrationGateway->findById($discountBody->integrationId);
            if(!isset($discountBody->discountId) || $discountBody->discountId==null || $discountBody->discountId=="0")
            {
                //echo json_encode("{'message': 'Create New'}");
                //var_dump($integration);
                $liArr = IntegrationController::PostDiscount($integration,$discountBody->gfMenuId);
            }
            else
            {
                $liArr = $integrationGateway->InsertOrUpdatePostedType($discountBody->discountName, "10", "discount", $discountBody->integrationId, $discountBody->gfMenuId, $discountBody->discountId, null, null);
            }
            echo json_encode($liArr);
            // $retval = (object)array(
            //     "draw" => 1,
            //     "recordsTotal" => count($liArr),
            //     "recordsFiltered" => count($liArr),
            //     "data" => ($liArr)
            // );
            //echo json_encode($retval);
            break;
        case 'PUT':
        case 'DELETE':
        default:
            break;
    }
}


function GetGFPromotion($UID, $integration_Id)
{
    global $dbConnection;

    $integrationGateway = new IntegrationGateway($dbConnection);
    $postedDiscounts = $integrationGateway->GetBatchTypeByIntegrationAndType($integration_Id, 'discount');

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
        $value->loyverseId = null;
        if (isset($postedDiscounts) && count($postedDiscounts) > 0 && array_key_exists($value->id, $postedDiscounts)) {
            $value->loyverseId = $postedDiscounts[$value->id]->loyverse_id;
        }
        # code...
    }
    curl_close($curl);
    return $responseObj;
}

function GetLoyveresDiscounts(int $integration_Id)
{
    global $dbConnection;
    $integration = (new IntegrationGateway($dbConnection))->findById($integration_Id);
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.loyverse.com/v1.0/discounts?show_deleted=false",
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
    //echo $response;
    if (isset(json_decode($response)->errors)) {
        return array(json_decode($response));
    }

    $usable_amount_discounts = array_filter(json_decode($response)->discounts, function ($obj) {
        return $obj->type == "VARIABLE_AMOUNT";
    });
    $usable_discounts["VARIABLE_AMOUNT"] = $usable_amount_discounts;

    // $usable_percentage_discounts = array_filter( json_decode($response)->discounts,function($obj){
    //     return $obj->type == "VARIABLE_PERCENT";
    // });
    // $usable_discounts["VARIABLE_PERCENT"] = $usable_percentage_discounts ; 

    //array_splice( $usable_discounts, 0, 0, array((object)array("id"=>null,"name"=>"< Create New >"))); 

    curl_close($curl);
    return $usable_discounts;
}
