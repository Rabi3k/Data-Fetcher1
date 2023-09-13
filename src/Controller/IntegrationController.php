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

        /*
        {
            "store_id": "42dc2cec-6f40-11ea-bde9-1269e7c5a22d",
            "order": "ORDER-103885",
            "customer_id": "c71758a2-79bf-11ea-bde9-1269e7c5a22d",
            "source": "My app",
            "receipt_date": "2020-06-23T08:35:47.047Z",
            "total_discounts": [
                {
                "id": "50f4e245-d221-448d-943a-7346c21cd82b",
                "percentage": 15,
                "scope": "LINE_ITEM"
                },
                {
                "id": "23a64c4f-c6e5-43cc-a017-b11a6ee32448",
                "scope": "RECEIPT"
                },
                {
                "id": "77d2bf40-7b12-11ea-bc55-0242ac130003",
                "money_amount": 5,
                "scope": "RECEIPT"
                }
            ],
            "line_items": [
                {
                "variant_id": "06929667-cc44-4bbb-b226-6758285d7033",
                "quantity": 2
                },
                {
                "variant_id": "706e2626-3329-45f8-98d7-0e1dbcbcb9d9",
                "quantity": 1,
                "price": 100,
                "cost": 50,
                "line_note": "Some line note",
                "line_discounts": [],
                "line_taxes": [],
                "line_modifiers": []
                }
            ],
            "note": "Some note for the receipt",
            "payments": [
                {
                "payment_type_id": "42dd2a55-6f40-11ea-bde9-1269e7c5a22d",
                "paid_at": "2020-06-10T19:16:46Z"
                }
            ]
        } 
        
        */
    }
    //     static private function PostCategory(string $catName)
    //     {

    //         $curl = curl_init();

    //         curl_setopt_array($curl, array(
    //             CURLOPT_URL => 'https://api.loyverse.com/v1.0/categories',
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_ENCODING => '',
    //             CURLOPT_MAXREDIRS => 10,
    //             CURLOPT_TIMEOUT => 0,
    //             CURLOPT_FOLLOWLOCATION => true,
    //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //             CURLOPT_CUSTOMREQUEST => 'POST',
    //             CURLOPT_POSTFIELDS => '{

    //     "name": "' . $catName . '",
    //     "color": "RED"

    // }',
    //             CURLOPT_HTTPHEADER => array(
    //                 'Content-Type: application/json',
    //                 'Authorization: Bearer '
    //             ),
    //         ));

    //         $response = curl_exec($curl);

    //         curl_close($curl);
    //         echo $response;
    //     }
    //     static public function PostCategories(array $categories)
    //     {
    //         foreach ($categories as $key => $value) {
    //             # code...
    //             IntegrationController::PostCategory($value);
    //         }
    //         return GeneralController::CreateResponser(implode(", ", $categories));
    //     }
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
