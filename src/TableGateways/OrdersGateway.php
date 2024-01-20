<?php

namespace Src\TableGateways;

use DateTime;
use DateTimeZone;
use Pinq\Traversable;
use Src\Classes\Loggy;
use Src\Classes\Order;
use Src\Enums\ItemStatus;
use Src\System\DbObject;

class OrdersGateway extends DbObject
{

    #region protected functions
    protected function SetSelectStatment()
    {
        $tblname = $this->getTableName();
        $this->selectStatment = "SELECT * FROM $tblname;";
    }
    protected function SetTableName()
    {
        $this->tblName = "tbl_order_head";
    }
    #endregion
    #region public functions
    public function findSoldItems(array $restauntRefId = array())
    {
        $tblname = $this->getTableName();
        $restauntRefId = count($restauntRefId) > 0 ? implode(",", $restauntRefId) : null;
        $statment = "SELECT
                oi.`type_id` 'id',
                oi.`name` 'name',
                count(distinct oi.`order_head_id`) 'order_count',
                sum(oi.`quantity`) as 'qty',
                sum(oi.`total_item_price`) - sum(oi.`item_discount`) as 'total_Earn'
            FROM `tbl_order_items` oi
                LEFT JOIN `tbl_order_head` o on oi.`order_head_id` = o.`id`
            WHERE o.`ready` = 0
                AND oi.`type`='item'
                AND o.`restaurant_id` in (IFNULL(:restaunt_id,o.`restaurant_id`))
            GROUP BY oi.`type_id`";
        // "SELECT JSON_UNQUOTE(
        //     JSON_EXTRACT(
        //         data,'$.items')) as 'items'
        //         FROM $tblname WHERE  
        //         JSON_UNQUOTE(
        //             JSON_EXTRACT(data,'$.ready')) = 'true'
        //             AND
        //             restaurant_refId = ifnull(:restauntRefId,restaurant_refId);";

        try {
            $statement = $this->getDbConnection()->prepare($statment);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array("restaunt_id" => $restauntRefId));
            $this->getDbConnection()->commit();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            $itemst = json_encode($result);
            $items = json_decode($itemst);
            return $items;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function FindByRestaurantRefId($id)
    {
        $tblname = $this->getTableName();
        $statment = "SELECT 
        SUM(oi_pi.item_discount) * COUNT(DISTINCT oi_pi.order_head_id) / COUNT(*)  AS 'promoItemValues',
        SUM(oi_pc.cart_discount)* COUNT(DISTINCT oi_pc.order_head_id) / COUNT(*) * -1 AS 'promoCartValues' ,
        SUM(oi_df.total_item_price) * COUNT(DISTINCT oi_df.order_head_id) / COUNT(*)         AS 'deliveryFee' ,
        oh.*
            FROM
        `tbl_order_head` oh
            LEFT JOIN
        `tbl_order_items` oi_pi ON (oh.id = oi_pi.order_head_id
            AND oi_pi.type = 'promo_item')
            LEFT JOIN
        `tbl_order_items` oi_pc ON (oh.id = oi_pc.order_head_id
            AND oi_pc.type = 'promo_cart')
            LEFT JOIN
        `tbl_order_items` oi_df ON (oh.id = oi_df.order_head_id
            AND oi_df.type = 'DELIVERY_FEE')
     
        where oh.restaurant_id = $id GROUP BY oh.id order by oh.fulfill_at desc limit 1000";
        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

            $results = array();
            foreach ($result as $row) {
                $results[] = new Order($row);
            }
            return $results;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function FindByRestaurantsRefId(array $id)
    {
        $tblname = $this->getTableName();
        $restauntRefId = count($id) > 0 ? implode(",", $id) : null;
        $statment = "SELECT * FROM `$tblname` o
         WHERE o.`restaurant_id` in (IFNULL(:restaunt_id,o.`restaurant_id`));";

        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $statement = $this->getDbConnection()->prepare($statment);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array("restaunt_id" => $restauntRefId));
            $this->getDbConnection()->commit();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            foreach ($result as $row) {
                $results[] = (new Order($row))->getjson();
            }
            return $results;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function FindById($id): Order|null
    {
        $tblname = $this->getTableName();
        $statment = "SELECT * FROM `tbl_order_head` 
        where id = $id";
        $statment_items = "SELECT * FROM `tbl_order_items` 
        where order_head_id = $id";
        $statment_taxlist = "SELECT * FROM `tbl_ordder_tax_list` 
        where order_head_id = $id";
        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            $o = new Order($result[0]);


            $statment_items = $this->getDbConnection()->query($statment_items);
            $result_items = $statment_items->fetchAll(\PDO::FETCH_ASSOC);
            $statment_items->closeCursor();

            $result_itemstxt = json_encode($result_items);
            $result_itemse = json_decode($result_itemstxt);

            $o->items = $result_itemse;
            if (count($o->items) > 0) {
                foreach ($o->items as $item) {
                    $statment_itemOptions = "SELECT * FROM  `tbl_order_item_options` 
                    where order_item_id = $item->id";
                    $statment_itemOptions = $this->getDbConnection()->query($statment_itemOptions);
                    $result_itemOptions = $statment_itemOptions->fetchAll(\PDO::FETCH_ASSOC);
                    $statment_itemOptions->closeCursor();

                    $result_itemOptionstxt = json_encode($result_itemOptions);
                    $result_itemOptions = json_decode($result_itemOptionstxt);

                    $item->options = $result_itemOptions;
                }
            }

            $statment_taxlist = $this->getDbConnection()->query($statment_taxlist);
            $result_taxList = $statment_taxlist->fetchAll(\PDO::FETCH_ASSOC);
            $statment_taxlist->closeCursor();

            $result_taxListtxt = json_encode($result_taxList);
            $result_taxListe = json_decode($result_taxListtxt);

            $o->tax_list = $result_taxListe;
            //echo ($o->getJsonStr());
            return $o;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function FindByDate($startDate, $endDate, array $secrets)
    {

        $tblname = $this->getTableName();
        $secretsJ = implode("','", $secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate = $endDate->format('Y-m-d H:i:s');
        $statment = "SELECT oh.* FROM `tbl_order_head` oh 
                        WHERE
                            oh.`fulfill_at` BETWEEN CAST('$sDate' as DateTime) AND CAST('$eDate' as DateTime)
                            And oh.`restaurant_key` IN ('$secretsJ')
                        ORDER BY oh.`fulfill_at`";
        //echo "secretsJ: $secretsJ \nstatment: $statment\n";
        try {
            $query = $this->getDbConnection()->query($statment);
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            $query->closeCursor();
            return Order::GetOrderList($result);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    /**Depreciated */
    public function GetItemsSold()
    {
        return $this->findSoldItems();
    }
    public function FindActiveIdsByDate($startDate, $endDate, array $secrets)
    {
        $tblname = $this->getTableName();
        $secretsJ = implode("','", $secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate = $endDate->format('Y-m-d H:i:s');
        $statment = "SELECT oh.`id` FROM `tbl_order_head` oh 
                        WHERE
                            oh.`ready` = 0
                            AND oh.`status` = 'accepted'
                            AND oh.`fulfill_at` BETWEEN CAST('$sDate' as DateTime) AND CAST('$eDate' as DateTime)
                            And oh.`restaurant_key` IN ('$secretsJ')
                        ORDER BY oh.`fulfill_at`";
        try {
            $query = $this->getDbConnection()->query($statment);
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            $query->closeCursor();
            $results = array();
            foreach ($result as $row) {
                $results[] =  $row['id'];
            }
            return $results;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function FindActiveIdsByDateRestaurantRefId($startDate, $endDate, array $refIds)
    {
        $tblname = $this->getTableName();
        $StrRefIds = implode(",", $refIds);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate = $endDate->format('Y-m-d H:i:s');
        $statment = "SELECT oh.`id` FROM `tbl_order_head` oh 
                        WHERE
                            oh.`ready` = 0
                            AND oh.`is_done` = 0
                            AND oh.`status` = 'accepted'
                            AND oh.`fulfill_at` BETWEEN CAST('$sDate' as DateTime) AND CAST('$eDate' as DateTime)
                            And oh.`restaurant_id` IN ($StrRefIds)
                        ORDER BY oh.`fulfill_at`";
        try {
            $query = $this->getDbConnection()->query($statment);

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);

            $query->closeCursor();
            $results = array();
            foreach ($result as $row) {
                $results[] = $row['id'];
            }
            return $results;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function FindCompletedByRestaurantRefId(array $refIds)
    {
        $tblname = $this->getTableName();
        $StrRefIds = implode(",", $refIds);
        $sDate = (new DateTime('now', new \DateTimeZone("UTC")))->sub(new \DateInterval('PT2H'))->format('Y-m-d H:i:s');
        $statment = "SELECT oh.* FROM `tbl_order_head` oh 
                        WHERE
                            oh.`ready` = 0
                            AND oh.`is_done` = 1
                            AND oh.`status` = 'accepted'
                            AND oh.`fulfill_at` > CAST('$sDate' as DateTime) 
                            And oh.`restaurant_id` IN ($StrRefIds)
                        ORDER BY oh.`fulfill_at`";
        try {
            $query = $this->getDbConnection()->query($statment);

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);

            $query->closeCursor();

            return $result;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function FindDoneByRestaurantRefIdAndDate($startDate, $endDate, array $refIds)
    {
        $tblname = $this->getTableName();
        $StrRefIds = implode(",", $refIds);


        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate = $endDate->format('Y-m-d H:i:s');
        $statment = "SELECT oh.* FROM `tbl_order_head` oh 
                        WHERE
                            oh.`ready` = 1
                            AND oh.`status` = 'accepted'
                            AND oh.`fulfill_at` BETWEEN CAST('$sDate' as DateTime) and CAST('$eDate' as DateTime) 
                            And oh.`restaurant_id` IN ($StrRefIds)
                        ORDER BY oh.`fulfill_at`";
        try {
            $query = $this->getDbConnection()->query($statment);

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);

            $query->closeCursor();

            return $result;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function FindActiveByDate($startDate, $endDate, array $secrets)
    {

        $tblname = $this->getTableName();
        $secretsJ = implode("','", $secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate = $endDate->format('Y-m-d H:i:s');
        $statment = "SELECT oh.* FROM `tbl_order_head` oh 
                        WHERE
                            oh.`ready` = 0
                            AND oh.`is_done` = 0
                            AND oh.`status` = 'accepted'
                            AND oh.`fulfill_at` BETWEEN CAST('$sDate' as DateTime) AND CAST('$eDate' as DateTime)
                            And oh.`restaurant_key` IN ('$secretsJ')
                        ORDER BY oh.`fulfill_at`";
        //echo "secretsJ: $secretsJ \nstatment: $statment\n";
        try {
            $query = $this->getDbConnection()->query($statment);
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            $query->closeCursor();
            $results = array();
            foreach ($result as $row) {
                $jObj = new Order($row);
                array_push($results, $jObj);
            }
            return $results;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    public function FindInHouseActiveIdsByDate($startDate, $endDate, array $secrets)
    {
        $tblname = $this->getTableName();
        $secretsJ = implode("','", $secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate = $endDate->format('Y-m-d H:i:s');
        $statment = "SELECT oh.`id` FROM `tbl_order_head` oh 
                        WHERE
                            oh.`ready` = 0
                            AND oh.`status` = 'accepted'
                            AND oh.`fulfill_at` BETWEEN CAST('$sDate' as DateTime) AND CAST('$eDate' as DateTime)
                            And oh.`restaurant_key` IN ('$secretsJ')
                            AND oh.`type` in ('pickup','table_reservation','order_ahead','dine_in')
                        ORDER BY oh.`fulfill_at`";
        //echo "secretsJ: $secretsJ \nstatment: $statment\n";
        //'pickup' , 'delivery' , 'table_reservation' , 'order_ahead' , 'dine_in'
        try {
            $query = $this->getDbConnection()->query($statment);
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            $query->closeCursor();
            $o = new Order($result[0]);
            $results = array();
            foreach ($result as $row) {
                $results[] = $row['id'];
            }
            return $results;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function FindInHouseActiveByDate($startDate, $endDate, array $secrets)
    {

        $tblname = $this->getTableName();
        $secretsJ = implode("','", $secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate = $endDate->format('Y-m-d H:i:s');
        $statment = "SELECT oh.* FROM `tbl_order_head` oh 
                        WHERE
                            oh.`ready` = 0
                            AND oh.`status` = 'accepted'
                            AND oh.`fulfill_at` BETWEEN CAST('$sDate' as DateTime) AND CAST('$eDate' as DateTime)
                            And oh.`restaurant_id` IN ($secretsJ)
                            AND oh.`type` in ('pickup','table_reservation','order_ahead','dine_in')
                        ORDER BY oh.`fulfill_at`";
        //echo $statment;
        //echo "secretsJ: $secretsJ \nstatment: $statment\n";
        //'pickup' , 'delivery' , 'table_reservation' , 'order_ahead' , 'dine_in'
        try {
            $query = $this->getDbConnection()->query($statment);
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            $query->closeCursor();
            $results = array();
            foreach ($result as $row) {
                $results[] = new Order($row);
            }
            return $results;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    public function InsertOrUpdate($data)
    {
        $order = json_decode($data);
        $this->InsertOrUpdate_orderHead($order);
        $this->Delete_OrderTaxList($order->id);
        foreach ($order->tax_list as  $ordderTax) {
            $ordderTax->order_head_id = $order->id;
            $this->InsertOrUpdate_OrderTaxList($ordderTax);
        }
        foreach ($order->items as  $orderItem) {
            $orderItem->order_head_id = $order->id;
            $orderItem->status = $order->ready == true ? ItemStatus::Complete->name : ItemStatus::ToDo->name;
            $orderItem->isDone = false;
            $this->InsertOrUpdate_OrderItemsList($orderItem);
            if (isset($orderItem->options) && count($orderItem->options) > 0) {
                foreach ($orderItem->options as  $option) {
                    $option->order_item_id = $orderItem->id;
                    $this->InsertOrUpdate_OrderItemOptionList($option);
                }
            }
        }
        if ($order->ready == true) {
            // add to queue Posts 
            $lorder = $order->PostOrderToLoyverse();
            (new Loggy())->info("Order {$lorder->id} Posted/ {$order->id}  Updated Successfully");
            return "Order {$lorder->id} Posted/ {$order->id}  Updated Successfully";
        }
        return "Order {$order->id} Created / Updated Successfully";
    }

    public function InsertOrUpdate_orderHead($order)
    {

        $statement = "INSERT INTO `tbl_order_head`
       (`id`,
       `api_version`,
        `type`,
        `status`,
        `missed_reason`,
        `persons`,
        `source`,
        `payment`,
        `gateway_type`,
        `gateway_transaction_id`,
        `accepted_at`,
        `fulfill_at`,
        `updated_at`,
        `for_later`,
        `instructions`,
        `restaurant_id`,
        `company_account_id`,
        `restaurant_name`,
        `restaurant_phone`,
        `restaurant_country`,
        `restaurant_city`,
        `restaurant_zipcode`,
        `restaurant_street`,
        `restaurant_latitude`,
        `restaurant_longitude`,
        `restaurant_timezone`,
        `restaurant_key`,
        `restaurant_token`,
        `currency`,
        `client_id`,
        `client_first_name`,
        `client_last_name`,
        `client_email`,
        `client_phone`,
        `client_address`,
        `client_address_parts`,
        `client_marketing_consent`,
        `client_language`,
        `fulfillment_option`,
        `table_number`,
        `ready`,
        `integration_payment_provider`,
        `integration_payment_amount`,
        `used_payment_methods`,
        `card_type`,
        `billing_details`,
        `pin_skipped`,
        `latitude`,
        `longitude`,
        `total_price`,
        `sub_total_price`,
        `tax_type`,
        `tax_name`,
        `tax_value`,
        `coupons`,
        `reference`,
        `pos_system_id`
        )
        VALUES
        (:id,
        :api_version,
        :type,
        :status,
        :missed_reason,
        :persons,
        :source,
        :payment,
        :gateway_type,
        :gateway_transaction_id,
        :accepted_at,
        :fulfill_at,
        :updated_at,
        :for_later,
        :instructions,
        :restaurant_id,
        :company_account_id,
        :restaurant_name,
        :restaurant_phone,
        :restaurant_country,
        :restaurant_city,
        :restaurant_zipcode,
        :restaurant_street,
        :restaurant_latitude,
        :restaurant_longitude,
        :restaurant_timezone,
        :restaurant_key,
        :restaurant_token,
        :currency,
        :client_id,
        :client_first_name,
        :client_last_name,
        :client_email,
        :client_phone,
        :client_address,
        :client_address_parts,
        :client_marketing_consent,
        :client_language,
        :fulfillment_option,
        :table_number,
        :ready,
        :integration_payment_provider,
        :integration_payment_amount,
        :used_payment_methods,
        :card_type,
        :billing_details,
        :pin_skipped,
        :latitude,
        :longitude,
        :total_price,
        :sub_total_price,
        :tax_type,
        :tax_name,
        :tax_value,
        :coupons,
        :reference,
        :pos_system_id)
                ON DUPLICATE KEY UPDATE
        `api_version` = :api_version,
        `type` = :type,
        `status` = :status,
        `missed_reason` = :missed_reason,
        `persons` = :persons,
        `source` = :source,
        `payment` = :payment,
        `gateway_type` = :gateway_type,
        `gateway_transaction_id` = :gateway_transaction_id,
        `accepted_at` = :accepted_at,
        `fulfill_at` = :fulfill_at,
        `updated_at` = :updated_at,
        `for_later` = :for_later,
        `instructions` = :instructions,
        `restaurant_id` = :restaurant_id,
        `company_account_id` = :company_account_id,
        `restaurant_name` = :restaurant_name,
        `restaurant_phone` = :restaurant_phone,
        `restaurant_country` = :restaurant_country,
        `restaurant_city` = :restaurant_city,
        `restaurant_zipcode` = :restaurant_zipcode,
        `restaurant_street` = :restaurant_street,
        `restaurant_latitude` = :restaurant_latitude,
        `restaurant_longitude` = :restaurant_longitude,
        `restaurant_timezone` = :restaurant_timezone,
        `restaurant_key` = :restaurant_key,
        `restaurant_token` = :restaurant_token,
        `currency` = :currency,
        `client_id` = :client_id,
        `client_first_name` = :client_first_name,
        `client_last_name` = :client_last_name,
        `client_email` = :client_email,
        `client_phone` = :client_phone,
        `client_address` = :client_address,
        `client_address_parts` = :client_address_parts,
        `client_marketing_consent` = :client_marketing_consent,
        `client_language` = :client_language,
        `fulfillment_option` = :fulfillment_option,
        `table_number` = :table_number,
        `ready` = :ready,
        `integration_payment_provider` = :integration_payment_provider,
        `integration_payment_amount` = :integration_payment_amount,
        `used_payment_methods` = :used_payment_methods,
        `card_type` = :card_type,
        `billing_details` = :billing_details,
        `pin_skipped` = :pin_skipped,
        `latitude` = :latitude,
        `longitude` = :longitude,
        `total_price` = :total_price,
        `sub_total_price` = :sub_total_price,
        `tax_type` = :tax_type,
        `tax_name` = :tax_name,
        `tax_value` = :tax_value,
        `coupons` = :coupons,
        `reference` = :reference,
        `pos_system_id` = :pos_system_id
        ;";

        try {


            $statement = $this->getDbConnection()->prepare($statement);

            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'id' => (int)$order->id,
                'api_version' => (int)$order->api_version,
                'type' => $order->type,
                'status' => $order->status,
                'missed_reason' => $order->missed_reason,
                'persons' => $order->persons,
                'source' => $order->source,
                'payment' => $order->payment,
                'gateway_type' => $order->gateway_type,
                'gateway_transaction_id' => $order->gateway_transaction_id,
                'accepted_at' => $order->accepted_at,
                'fulfill_at' => $order->fulfill_at,
                'updated_at' => $order->updated_at,
                'for_later' => $order->for_later,
                'instructions' => $order->instructions,
                'restaurant_id' => $order->restaurant_id,
                'company_account_id' => $order->company_account_id,
                'restaurant_name' => $order->restaurant_name,
                'restaurant_phone' => $order->restaurant_phone,
                'restaurant_country' => $order->restaurant_country,
                'restaurant_city' => $order->restaurant_city,
                'restaurant_zipcode' => $order->restaurant_zipcode,
                'restaurant_street' => $order->restaurant_street,
                'restaurant_latitude' => $order->restaurant_latitude,
                'restaurant_longitude' => $order->restaurant_longitude,
                'restaurant_timezone' => $order->restaurant_timezone,
                'restaurant_key' => $order->restaurant_key,
                'restaurant_token' => $order->restaurant_token,
                'currency' => $order->currency,
                'client_id' => $order->client_id,
                'client_first_name' => $order->client_first_name,
                'client_last_name' => $order->client_last_name,
                'client_email' => $order->client_email,
                'client_phone' => $order->client_phone,
                'client_address' => $order->client_address,
                'client_address_parts' => json_encode($order->client_address_parts),
                'client_marketing_consent' => $order->client_marketing_consent,
                'client_language' => $order->client_language,
                'fulfillment_option' => $order->fulfillment_option,
                'table_number' => $order->table_number,
                'ready' => $order->ready,
                'integration_payment_provider' => $order->integration_payment_provider,
                'integration_payment_amount' => $order->integration_payment_amount,
                'used_payment_methods' => json_encode($order->used_payment_methods),
                'card_type' => $order->card_type,
                'billing_details' => $order->billing_details,
                'pin_skipped' => $order->pin_skipped,
                'latitude' => $order->latitude,
                'longitude' => $order->longitude,
                'total_price' => $order->total_price,
                'sub_total_price' => $order->sub_total_price,
                'tax_type' => $order->tax_type,
                'tax_name' => $order->tax_name,
                'tax_value' => $order->tax_value,
                'coupons' => json_encode($order->coupons),
                'reference' => $order->reference,
                'pos_system_id' => $order->pos_system_id
            ));
            $statement->closeCursor();

            $this->getDbConnection()->commit();
            (new Loggy())->info("new `tbl_order_head` Created / Updated: order_id => $order->id, restaurant_id => $order->restaurant_id");
            return $statement->rowCount();
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    public function InsertOrUpdate_OrderTaxList($orderTaxList)
    {

        $statement = "INSERT INTO `tbl_ordder_tax_list`
        (`order_head_id`,
        `type`,
        `value`,
        `rate`)
        VALUES
        (:order_head_id,
        :type,
        :value,
        :rate)
        ON DUPLICATE KEY UPDATE
        `value` = :value;";

        try {


            $statement = $this->getDbConnection()->prepare($statement);

            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'order_head_id' => $orderTaxList->order_head_id,
                'type' => $orderTaxList->type,
                'value' => $orderTaxList->value,
                'rate' => $orderTaxList->rate
            ));
            $statement->closeCursor();

            $this->getDbConnection()->commit();
            return $statement->rowCount();
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function InsertOrUpdate_OrderItemOptionList($orderItemOption)
    {

        $statement = "INSERT INTO `tbl_order_item_options`
        (`id`,
        `order_item_id`,
        `name`,
        `group_name`,
        `type`,
        `type_id`,
        `quantity`,
        `price`)
        VALUES
        (:id,
        :order_item_id,
        :name,
        :group_name,
        :type,
        :type_id,
        :quantity,
        :price)
        ON DUPLICATE KEY UPDATE
        `name` = :name,
        `group_name` = :group_name,
        `type` = :type,
        `type_id` = :type_id,
        `quantity` = :quantity,
        `price` = :price;";

        try {


            $statement = $this->getDbConnection()->prepare($statement);

            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'id' => $orderItemOption->id,
                'order_item_id' => $orderItemOption->order_item_id,
                'name' => $orderItemOption->name,
                'group_name' => $orderItemOption->group_name,
                'type' => $orderItemOption->type,
                'type_id' => $orderItemOption->type_id,
                'quantity' => $orderItemOption->quantity,
                'price' => $orderItemOption->price
            ));
            $statement->closeCursor();

            $this->getDbConnection()->commit();
            return $statement->rowCount();
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function Delete_OrderTaxList($order_head_id)
    {

        $statement = "DELETE FROM `tbl_ordder_tax_list`
        WHERE order_head_id = :order_head_id;";

        try {


            $statement = $this->getDbConnection()->prepare($statement);

            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'order_head_id' => $order_head_id
            ));
            $statement->closeCursor();

            $this->getDbConnection()->commit();
            return $statement->rowCount();
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    public function InsertOrUpdate_OrderItemsList($orderItemList)
    {

        $statement = "INSERT INTO `tbl_order_items`
        (`id`,
        `order_head_id`,
        `name`,
        `instructions`,
        `type`,
        `type_id`,
        `parent_id`,
        `total_item_price`,
        `tax_type`,
        `tax_value`,
        `tax_rate`,
        `price`,
        `quantity`,
        `item_discount`,
        `cart_discount`,
        `cart_discount_rate`,
        `coupon`,
        `status`)
        VALUES
        (:id,
        :order_head_id,
        :name,
        :instructions,
        :type,
        :type_id,
        :parent_id,
        :total_item_price,
        :tax_type,
        :tax_value,
        :tax_rate,
        :price,
        :quantity,
        :item_discount,
        :cart_discount,
        :cart_discount_rate,
        :coupon,
        :status)
        ON DUPLICATE KEY UPDATE
        `name` = :name,
        `instructions` = :instructions,
        `type` = :type,
        `type_id` = :type_id,
        `parent_id` = :parent_id,
        `total_item_price` = :total_item_price,
        `tax_type` = :tax_type,
        `tax_value` = :tax_value,
        `tax_rate` = :tax_rate,
        `price` = :price,
        `quantity` = :quantity,
        `item_discount` = :item_discount,
        `cart_discount` = :cart_discount,
        `cart_discount_rate` = :cart_discount_rate,
        `coupon` = :coupon,
        `status` = :status;";

        try {


            $statement = $this->getDbConnection()->prepare($statement);
            $coupon = Null;
            if (isset($orderItemList->coupon)) {
                $coupon = $orderItemList->coupon;
            }
            $status = ItemStatus::ToDo->name;
            if (isset($orderItemList->status)) {
                $status = $orderItemList->status;
            }

            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'id' => $orderItemList->id,
                'order_head_id' => $orderItemList->order_head_id,
                'name' => $orderItemList->name,
                'instructions' => $orderItemList->instructions,
                'type' => $orderItemList->type,
                'type_id' => $orderItemList->type_id,
                'parent_id' => $orderItemList->parent_id,
                'total_item_price' => $orderItemList->total_item_price,
                'tax_type' => $orderItemList->tax_type,
                'tax_value' => $orderItemList->tax_value,
                'tax_rate' => $orderItemList->tax_rate,
                'price' => $orderItemList->price,
                'quantity' => $orderItemList->quantity,
                'item_discount' => $orderItemList->item_discount,
                'cart_discount' => $orderItemList->cart_discount,
                'cart_discount_rate' => $orderItemList->cart_discount_rate,
                'coupon' => $coupon,
                'status' => $status,
            ));
            $statement->closeCursor();

            $this->getDbConnection()->commit();
            return $statement->rowCount();
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    public function UpdateOrderStatus(int $orderId, bool $status)
    {
        $statment = "UPDATE `tbl_order_head` oh 
        left join `tbl_order_items` oi on(oh.id=oi.order_head_id)
        SET
        oh.`is_done` = :status,
        oi.`status` = :orderItemStatus
        WHERE 
        oh.`id`= :orderId;
        ";

        $orderItemStatus = $status == true ? ItemStatus::Complete->name : ItemStatus::ToDo->name;
        try {
            $statment = $this->getDbConnection()->prepare($statment);
            $this->getDbConnection()->beginTransaction();
            $statment->execute(array(
                'orderId' => intval($orderId),
                'status' => $status,
                'orderItemStatus' => $orderItemStatus,
            ));
            $statment->closeCursor();

            $this->getDbConnection()->commit();
            return $statment->rowCount();
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    #endregion
}
