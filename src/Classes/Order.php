<?php

namespace Src\Classes;

use DateTime;
use Src\Classes\ClassObj;
use Src\TableGateways\IntegrationGateway;
use Src\TableGateways\PaymentRelationGateway;
use stdClass;

class Order extends ClassObj
{
    public bool $isDone = false;
    #region Private props
    #endregion

    #region Construct
    public function __construct($data = new \stdClass())
    {
        $this->LoadDataObject($data);
    }
    #endregion

    #region private functions
    #endregion

    #region protected functions
    protected function LoadDataObject($data)
    {
        $this->data = $data;
    }
    #endregion

    #region public functions
    public function LoadOrder($orderObj)
    {

        $this->LoadDataObject($orderObj);
    }

    #endregion
    #region static function
    public static function GetOrder($orderObj)
    {
        $order = new Order();
        $order->LoadOrder($orderObj);
        return $order;
    }
    public static function GetOrderList(array $orders)
    {
        $retval = array();
        foreach ($orders as $o) {
            $retval[] = new Order($o);
        }
        return $retval;
    }
    #endregion
    #region Loyveres Order
    public function findLClient($integration, $integrationGateway): string
    {
        $customer_id = "";
        $customer = $integrationGateway->GetTypeByIntegrationAndGfId(intval($this->client_email), $integration->Id, "customer");
        if (isset($customer)) {

            $customer_id = $customer->loyverse_id ?? "";
        } else {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.loyverse.com/v1.0/customers?email=$this->client_email",
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

            $response = json_decode(curl_exec($curl))->customers;
            curl_close($curl);

            if (count($response) > 0) {
                $customer_id = $response[0]->id;
            } else {
                $curl = curl_init();
                /*{    
                        "name": "<string>",    
                        "email": "<string>",    
                        "phone_number": "<string>",    
                        "address": "<string>",    
                        "city": "<string>",    
                        "postal_code": "<string>",    
                        "country_code": "<string>",    
                        "customer_code": "<string>"
                    } */
                if (isset($this->client_address_parts)) {
                    $addressPart = json_decode(mb_convert_encoding($this->client_address_parts, 'UTF-8'));
                }
                $customerData = (object)array(
                    "name" => ("$this->client_first_name $this->client_last_name"),
                    "email" => "$this->client_email",
                    "phone_number" => "$this->client_phone",
                    "address" => "$addressPart->street",
                    "city" => "$addressPart->city",
                    "postal_code" => "$addressPart->zipcode",
                    "country_code" => "DK",
                    "customer_code" => "$this->client_id"
                );

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.loyverse.com/v1.0/customers',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($customerData),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        "Authorization: Bearer $integration->LoyverseToken"
                    ),
                ));

                $response = json_decode(curl_exec($curl));
                $respItem = $integrationGateway->InsertOrUpdatePostedType("$this->client_first_name $this->client_last_name", $this->client_id, "customer", $integration->Id, 0, $response->id, null, null);
                curl_close($curl);
                //echo json_encode($customerData);
                $customer_id = $respItem->loyverse_id;
            }
        }

        return $customer_id;
    }
    public function ToLoyverseOrder(): LOrder|NULL
    {
        global $dbConnection;
        $integrationGateway = (new IntegrationGateway($dbConnection));
        $integration = $integrationGateway->findAllByRestaurantIds(array(intval($this->restaurant_id)))[0];
        
        $paymentRelationGateway = (new PaymentRelationGateway($dbConnection));
        $defaultPayment = $paymentRelationGateway->findByKey($integration->Id, "default");
        $payment = $paymentRelationGateway->findByKey($integration->Id, $this->payment);
        if ($payment == null || $payment->LoyveresId == "0") {
            $payment = $defaultPayment;
        }

        //if integration not exist return null
        //get customer from $this->client_email if not exist Create new in Loyverse
        //
        $customer_id = $this->findLClient($integration, $integrationGateway);


        $lineItems = array();
        $totalDiscounts = array();
        foreach ($this->items as $item) {
            # code...
            if ($item->type == "item") {
                $itemsLIds = $integrationGateway->GetTypeByIntegrationAndGfId(intval($item->type_id), $integration->Id, "item")->parent_lid;
                $modifier_options = array();
                foreach ($item->options as $option) {
                    # code...
                    if ($option->type == "size") {
                        $itemsLIds = $integrationGateway->GetTypeByIntegrationAndGfId(intval($option->type_id), $integration->Id, "variant")->loyverse_id;
                    } else if ($option->type == "option") {

                        $optionsLIds = $integrationGateway->GetTypeByIntegrationAndGfId(intval($option->type_id), $integration->Id, "option")->loyverse_id;
                        $modifier_options[] = (object)array("modifier_option_id" => $optionsLIds, "price" => $option->price);
                    }
                }
                $lineItems[] = (object)array("variant_id" => $itemsLIds, "quantity" => $item->quantity, "price" => $item->price, "line_modifiers" => $modifier_options, "line_note" => $item->instructions);

                //new LineItems($itemsLIds->loyverse_id,$item->quantity,null,null,"");
                //$itemsLIds->options = array();
                //$itemsLIds->options =$optionsLIds;
                //var_dump($itemsLIds);
            } else if ($item->type == "delivery_fee") {
                $itemsLIds = $integrationGateway->GetTypeByIntegrationAndGfId(1, $integration->Id, "delivery_fee")->parent_lid;

                $optionsLIds = array();
                foreach ($item->options as $option) {
                    # code...
                    if ($option->type == "size") {
                        $itemsLIds = $integrationGateway->GetTypeByIntegrationAndGfId(intval($option->type_id), $integration->Id, "variant")->loyverse_id;
                    } else if ($option->type == "option") {
                        $optionsLIds[] = $integrationGateway->GetTypeByIntegrationAndGfId(intval($option->type_id), $integration->Id, "option")->loyverse_id;
                    }
                }
                $lineItems[] = (object)array("variant_id" => $itemsLIds, "quantity" => $item->quantity, "price" => $item->price);
            } else if ($item->type == "promo_item" || $item->type == "promo_cart") {
                $PromotionsIds = $integrationGateway->GetTypeByIntegrationAndGfId(10, $integration->Id, "discount")->loyverse_id;
                if (count($totalDiscounts) > 0) {
                    $totalDiscounts[0]->money_amount += $item->item_discount;
                } else {
                    $totalDiscounts[0] = (object)array("id" => $PromotionsIds, "scope" => "RECEIPT", "money_amount" => $item->item_discount);
                }
            }
        }
        /*
        public string $paymentTypeId;
	public string $paidAt; */
        $date = new DateTime($this->fulfill_at);

        $payments = array((object)array("payment_type_id" => $payment->LoyveresId));
        $order = new LOrder(
            $integration->StoreId,
            "$this->type #$this->id",
            $customer_id,
            "Relax",
            $date->format('Y-m-d\TH:i:s\Z'),
            $totalDiscounts,
            $lineItems,
            "",
            $payments
        );
        return $order;
    }
    public function PostOrderToLoyverse()
    {
        global $dbConnection;
        $integrationGateway = (new IntegrationGateway($dbConnection));
        $integration = $integrationGateway->findAllByRestaurantIds(array(intval($this->restaurant_id)));
        if(!isset($integration) || count($integration)<1)
        {
            return;
        }

        $lOrder= json_encode($this->ToLoyverseOrder());
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.loyverse.com/v1.0/receipts',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $lOrder,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                "Authorization: Bearer $integration[0]->LoyverseToken"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }
    #endregion
}
