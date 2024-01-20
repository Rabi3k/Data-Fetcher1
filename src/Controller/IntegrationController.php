<?php

namespace Src\Controller;

use PhpParser\Node\Expr\Cast\Object_;
use Src\Classes\Integration;
use Src\Classes\Order;
use Src\TableGateways\IntegrationGateway;

class IntegrationController
{
    private $db;
    private $requestMethod;
    private $orderId;
    private array $secrets;


    public function __construct($db, $requestMethod, int $orderId = null, array $secrets = array())
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->orderId = $orderId ?? null;
        $this->secrets = $secrets;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                break;
            case 'POST':
                break;
            case 'PUT':
            case 'DELETE':
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }
    static function PostDiscount(Integration $integration,int $gf_menu_id )
    {
        global $dbConnection;
        $integrationGateway = new IntegrationGateway($dbConnection);
        //echo json_encode($cat);

        $curl = curl_init();

        $datas = (object)array(
            
            "type" => "VARIABLE_AMOUNT",
            "name" => "Online Rabat",
            "stores" => array("$integration->StoreId"),
            "restricted_access" => true,
        );

        $data = json_encode($datas);
        //echo $data;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.loyverse.com/v1.0/discounts',
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
        $l_id = $resp->id;
       
        $respItem = $integrationGateway->InsertOrUpdatePostedType("Online Rabat", "10", "discount", $integration->Id, $gf_menu_id, $l_id, null, null);
        return $respItem;
    }
    static function PostFeeItem(Integration $integration,int $gf_menu_id)
    {
        global $dbConnection;
        $integrationGateway = new IntegrationGateway($dbConnection);
        //echo json_encode($cat);

        $curl = curl_init();

        $datas = (object)array(
            "item_name" => "Delivery Fee",
            "reference_id" => "delivery_fee",
            "description" => "",
            "is_composite" => false,
            "option1_name" => null,
            "option2_name" => null,
            "option3_name" => null,
            "sold_by_weight" => false,
            "track_stock" => false,
            "use_production" => false,
            "tax_ids" => array("$integration->TaxId"),
            "variants" => array(),
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
        $m_id = $resp->id;
        $optIds = array();
        foreach ($resp->variants as $key => $value) {
            $optIds[] = $value->variant_id;
        }

        
        $l_ids = (object)array(
            "l_id" => $m_id,
            "ol_id" => $optIds
        );
        $respItem = $integrationGateway->InsertOrUpdatePostedType("Delivery Fee", "1", "delivery_fee", $integration->Id, $gf_menu_id, $l_ids->l_id, NULL, $l_ids->ol_id[0]);
        return $respItem;
    }
    static public function GetPromotion($UID,$integration_Id)
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
    static public function PostOrder(Order $order)
    {
        global $dbConnection;
        $integrationGateway = new IntegrationGateway($dbConnection);
        $integration = $integrationGateway->findAllByRestaurantIds(array($order->restaurant_id))[0];
        IntegrationController::GfOrderToLoReceipt($order, $integration);
        //
    }
    static private function GfOrderToLoReceipt(Order $order, Integration $integration)
    {
        $clientId = ""; //fetch cclient id by client email
        $discounts = array();
        $items = array();


        $lOrder = (object) array(
            "store_id" => $integration->StoreId,
            "order" => "$order->type-$order->id",
            "customer_id" => "$clientId",
            "source" => "Relax",

        );

    }
    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
