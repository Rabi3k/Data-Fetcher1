<?php

namespace Src\Classes;

use Src\Classes\ClassObj;
use Src\TableGateways\IntegrationGateway;
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
    public function ToLoyverseOrder(): LOrder|NULL
    {
        global $dbConnection;
        $integrationGateway = (new IntegrationGateway($dbConnection));
        $integration = $integrationGateway->findAllByRestaurantIds(array(intval($this->restaurant_id)))[0];
        //if integration not exist return null
        //get customer from $this->client_email if not exist Create new in Loyverse
        //
        $lineItems = array();
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
                        $modifier_options[] = (object)array("modifier_option_id" => $optionsLIds->loyverse_id, "price" => $option->price);
                    }
                }
                $lineItems[] = (object)array("variantId" => $itemsLIds, "quantity" => $item->quantity, "price" => $item->total_item_price, "lineModifiers" => $modifier_options, "line_note" => $item->instructions);

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
                $lineItems[] = (object)array("variantId" => $itemsLIds, "quantity" => $item->quantity, "price" => $item->total_item_price);

                //new LineItems($itemsLIds->loyverse_id,$item->quantity,null,null,"");
                //$itemsLIds->options = array();
                //$itemsLIds->options =$optionsLIds;
                //var_dump($itemsLIds);
            }
        }
        $order = new LOrder(
            $integration->StoreId,
            "$this->client_first_name $this->client_last_name |# $this->id",
            "",
            "Relax",
            $this->fulfill_at,
            array(),
            $lineItems,
            "",
            array()
        );
        return $order;
    }

    #endregion
}
