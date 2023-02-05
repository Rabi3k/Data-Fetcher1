<?php

namespace Src\TableGateways;

use Pinq\Traversable;
use Src\Classes\Loggy;
use Src\System\DbObject;

class OrdersGateway extends DbObject
{

    #region abstract functions
    protected function SetSelectStatment()
    {
        $tblname = $this->getTableName();
        $this->selectStatment = "SELECT * FROM $tblname;";
    }
    protected function SetTableName()
    {
        $this->tblName = "tbl_order";
    }
    public function findSoldItems($restauntRefId = null)
    {
        $tblname = $this->getTableName();
        $statment = "SELECT JSON_UNQUOTE(
            JSON_EXTRACT(
                data,'$.items')) as 'items'
                FROM $tblname WHERE  
                JSON_UNQUOTE(
                    JSON_EXTRACT(data,'$.ready')) = 'true'
                    AND
                    restaurant_refId = ifnull(:restauntRefId,restaurant_refId);";

        try {
            $statement = $this->getDbConnection()->prepare($statment);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'restauntRefId' => $restauntRefId,
            ));
            $this->getDbConnection()->commit();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $itemsTr = Traversable::from($result);
            $itemsarray = $itemsTr->selectMany(function ($x) {
                return json_decode($x['items']);
            })->asArray();
            return $itemsarray;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function FindByRestaurantRefId($id)
    {
        $tblname = $this->getTableName();
        $statment = "SELECT * FROM `$tblname` 
        where restaurant_refId = $id";
        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $orders = Traversable::from($result);

            $results = array();
            foreach ($result as $row) {
                $jObj = json_decode($row['data']);
                array_push($results, $jObj);
            }
            return $results;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function FindByRestaurantsRefId(array $id)
    {
        $tblname = $this->getTableName();
        $ids = implode(",", $id);
        $statment = "SELECT * FROM `$tblname` 
        where restaurant_refId in ($ids)";
        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

            $results = array();
            foreach ($result as $row) {
                $jObj = json_decode($row['data']);
                array_push($results, $jObj);
            }
            return $results;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function FindById($id)
    {
        $tblname = $this->getTableName();
        $statment = "SELECT * FROM `$tblname` 
        where order_id = $id";
        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $Orders = array_column($result, 'data');
            $results = array();
            foreach ($result as $row) {
                $jObj = json_decode($row['data']);
                array_push($results, $jObj);
            }
            return $results;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function FindByDate($startDate, $endDate, array $secrets)
    {

        $tblname = $this->getTableName();
        $secretsJ = implode("','", $secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate = $endDate->format('Y-m-d H:i:s');
        $statment = "SELECT * FROM `$tblname` 
                        WHERE
                        IFNULL(
                            CAST(
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.fulfill_at')) as DateTime),
                            CAST(
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.updated_at')) AS DATETIME))
                                    BETWEEN CAST('$sDate' as DateTime) 
                                    AND CAST('$eDate' as DateTime)
                                    
                                    And JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.restaurant_key')) in ('$secretsJ')";
        //echo "secretsJ: $secretsJ \nstatment: $statment\n";
        try {
            $query = $this->getDbConnection()->query($statment);
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            $results = array();
            foreach ($result as $row) {
                $jObj = json_decode($row['data']);
                array_push($results, $jObj);
            }
            return $results;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function GetItemsSold()
    {
        $items = $this->findSoldItems();
        $strings = Traversable::from($items);
        $results = array();
        foreach ($strings->where(function ($y) {
            return $y->type === "item";
        })->groupBy(function ($i) {
            return $i->type_id;
        })
            ->asArray() as $id => $itemGrp) {
            //echo $id;
            $qty = 0;
            $name = '';
            foreach ($itemGrp as $grp) {
                $qty =  $qty + $grp->quantity;
                $name = $grp->name;
            }
            $item = [
                'id' => $id,
                'qty' => $qty,
                'name' => $name
            ];
            array_push($results, $item);
        }

        //var_dump($results); 

        return $results;
    }
    public function FindActiveIdsByDate($startDate, $endDate, array $secrets)
    {

        $tblname = $this->getTableName();
        $secretsJ = implode("','", $secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate = $endDate->format('Y-m-d H:i:s');
        $statment = "SELECT JSON_EXTRACT(data,'$.id') as id,JSON_UNQUOTE(JSON_EXTRACT(data,'$.ready'))='true' as 'ready'
                     FROM `$tblname` 
                        WHERE
                        IFNULL(
                            CAST(
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.fulfill_at')) as DateTime),
                            CAST(
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.updated_at')) AS DATETIME))
                                    BETWEEN CAST('$sDate' as DateTime) 
                                    AND CAST('$eDate' as DateTime)
                                    
                                    And JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.restaurant_key')) in ('$secretsJ')
                                    And JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.status')) in ('accepted')

                                    group by order_id,restaurant_refId
                                    having MAX(ready)=0
                                    ORDER BY   CAST(JSON_UNQUOTE(JSON_EXTRACT(data,'$.fulfill_at')) as DateTime)
                                    ";
        //echo "secretsJ: $secretsJ \nstatment: $statment\n";
        try {
            $query = $this->getDbConnection()->query($statment);
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            $results = array();
            foreach ($result as $row) {
                array_push($results, intval($row['id']));
            }
            return $results;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function FindActiveByDate($startDate, $endDate, array $secrets)
    {

        $tblname = $this->getTableName();
        $secretsJ = implode("','", $secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate = $endDate->format('Y-m-d H:i:s');
        $statment = "SELECT 
                        JSON_UNQUOTE(JSON_EXTRACT(data,'$.ready'))='true' as 'ready',
                        o.* 
                        FROM `$tblname` o
                        WHERE
                        IFNULL(
                            CAST(
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.fulfill_at')) as DateTime),
                            CAST(
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.updated_at')) AS DATETIME))
                                    BETWEEN CAST('$sDate' as DateTime) 
                                    AND CAST('$eDate' as DateTime)
                                    
                                    And JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.restaurant_key')) in ('$secretsJ')

                                    And JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.status')) in ('accepted')

                                    group by order_id,restaurant_refId
                                    having MAX(ready)=0
                                    ORDER BY CAST(JSON_UNQUOTE(JSON_EXTRACT(data,'$.fulfill_at')) as DateTime)
                                    ";
        //echo "secretsJ: $secretsJ \nstatment: $statment\n";
        try {
            $query = $this->getDbConnection()->query($statment);
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            $results = array();
            foreach ($result as $row) {
                $jObj = json_decode($row['data']);
                array_push($results, $jObj);
            }
            return $results;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function FindInHouseActiveIdsByDate($startDate, $endDate, array $secrets)
    {

        $tblname = $this->getTableName();
        $secretsJ = implode("','", $secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate = $endDate->format('Y-m-d H:i:s');
        $statment = "SELECT JSON_EXTRACT(data,'$.id') as id,JSON_UNQUOTE(JSON_EXTRACT(data,'$.ready'))='true' as 'ready'
                     FROM `$tblname` 
                        WHERE
                        IFNULL(
                            CAST(
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.fulfill_at')) as DateTime),
                            CAST(
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.updated_at')) AS DATETIME))
                                    BETWEEN CAST('$sDate' as DateTime) 
                                    AND CAST('$eDate' as DateTime)
                                    
                                    And JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.restaurant_key')) in ('$secretsJ')
                                    And JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.status')) in ('accepted')
                                    And JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.type')) in ('table_reservation','dine_in','order_ahead','pickup')
                                    
                                    group by order_id,restaurant_refId
                                    having MAX(ready)=0
                                    ORDER BY   CAST(JSON_UNQUOTE(JSON_EXTRACT(data,'$.fulfill_at')) as DateTime)
                                    ";
        //echo "secretsJ: $secretsJ \nstatment: $statment\n";
        try {
            $query = $this->getDbConnection()->query($statment);
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            $results = array();
            foreach ($result as $row) {
                array_push($results, intval($row['id']));
            }
            return $results;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function FindInHouseActiveByDate($startDate, $endDate, array $secrets)
    {

        $tblname = $this->getTableName();
        $secretsJ = implode("','", $secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate = $endDate->format('Y-m-d H:i:s');
        $statment = "SELECT 
                        JSON_UNQUOTE(JSON_EXTRACT(data,'$.ready'))='true' as 'ready',
                        o.* 
                        FROM `$tblname` o
                        WHERE
                        IFNULL(
                            CAST(
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.fulfill_at')) as DateTime),
                            CAST(
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.updated_at')) AS DATETIME))
                                    BETWEEN CAST('$sDate' as DateTime) 
                                    AND CAST('$eDate' as DateTime)
                                    
                                    And JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.restaurant_key')) in ('$secretsJ')

                                    And JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.status')) = 'accepted'

                                    And JSON_UNQUOTE(
                                    JSON_EXTRACT(data,'$.type')) in ('table_reservation','dine_in','order_ahead','pickup')

                                    group by order_id,restaurant_refId
                                    having MAX(ready)=0
                                    ORDER BY CAST(JSON_UNQUOTE(JSON_EXTRACT(data,'$.fulfill_at')) as DateTime)
                                    ";
        //echo "secretsJ: $secretsJ \nstatment: $statment\n";
        try {
            $query = $this->getDbConnection()->query($statment);
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            $results = array();
            foreach ($result as $row) {
                $jObj = json_decode($row['data']);
                array_push($results, $jObj);
            }
            return $results;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function InsertOrUpdate($data, $order_id, $restaurant_refId)
    {

        $statement = "INSERT INTO `tbl_order`
        (`data`,
        `order_id`,
        `restaurant_refId`)
        VALUES 
        (:data,:order_id,:restaurant_refId)
        ON DUPLICATE KEY UPDATE
        `data` = :data;";

        try {
            $order = json_decode($data);
            foreach ($order->items as $item) {
                # code...
                $item->completed = ($order->ready != true && $item->completed != true) ? false : true;
            }
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'data' => $data,
                'order_id' => $order_id,
                'restaurant_refId' => $restaurant_refId,
            ));
            $this->getDbConnection()->commit();
            (new Loggy())->info("new order Created / Updated: order_id => $order_id, restaurant_refId => $restaurant_refId");
            /*  $oDate = new \DateTime();
                $oDate->setTimezone( new \DateTimeZone('Europe/Copenhagen'));
                if (!file_exists("logs/".$oDate->format('dmY'))) {
                    mkdir("logs/".$oDate->format('dmY'), 0777, true);
                }
                $myfile = fopen("logs/".$oDate->format('dmY')."/Log_".$input->order_id.".txt", "w")
                                or die("Unable to open file!");
                $inputStr ="Test 123123:".json_encode($input)."\n\r SqlStatment:".json_encode($statement);
                fwrite($myfile, $inputStr);
                fclose($myfile);*/

            return $statement->rowCount();
        } catch (\PDOException $e) {
            $_SESSION['loggy']->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    #endregion
}
