<?php
namespace Src\TableGateways;

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
    
    public function FindById($id)
    {
        $tblname = $this->getTableName();
        $statment = "SELECT * FROM `$tblname` 
        where JSON_EXTRACT(data,'$.id') = $id";
        //echo "ID: $id <br/>statment: $statment<br/>";
            try {
                $statement = $this->getDbConnection()->query($statment);
                $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
                $Orders = array_column($result, 'data');
                $results = array();
                foreach($result as $row)
                {
                    $jObj = json_decode($row['data']);
                    array_push($results,$jObj);
                }
                return $results;
            } catch (\PDOException $e) {
                exit($e->getMessage());
            }   

    }
    public function FindByDate($startDate,$endDate,array $secrets)
    {

        $tblname = $this->getTableName();
        $secretsJ = implode("','",$secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate =$endDate->format('Y-m-d H:i:s');
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
                foreach($result as $row)
                {
                    $jObj = json_decode($row['data']);
                    array_push($results,$jObj);
                }
                return $results;
                

            } catch (\PDOException $e) {
                exit($e->getMessage());
            }   

    }
    public function FindActiveIdsByDate($startDate,$endDate,array $secrets)
    {

        $tblname = $this->getTableName();
        $secretsJ = implode("','",$secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate =$endDate->format('Y-m-d H:i:s');
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
                                    group by JSON_EXTRACT(data,'$.id') having MAX(ready)=0
                                    ";
        //echo "secretsJ: $secretsJ \nstatment: $statment\n";
            try {
                $query = $this->getDbConnection()->query($statment);
                $result = $query->fetchAll(\PDO::FETCH_ASSOC);
                $results = array();
                foreach($result as $row)
                {
                    $jObj = json_decode($row['data']);
                    array_push($results,$jObj);
                }
                return $results;
                

            } catch (\PDOException $e) {
                exit($e->getMessage());
            }   

    }
    public function FindActiveByDate($startDate,$endDate,array $secrets)
    {

        $tblname = $this->getTableName();
        $secretsJ = implode("','",$secrets);
        $sDate = $startDate->format('Y-m-d H:i:s');
        $eDate =$endDate->format('Y-m-d H:i:s');
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
                                    group by JSON_EXTRACT(data,'$.id') having MAX(ready)=0
                                    ";
        //echo "secretsJ: $secretsJ \nstatment: $statment\n";
            try {
                $query = $this->getDbConnection()->query($statment);
                $result = $query->fetchAll(\PDO::FETCH_ASSOC);
                $results = array();
                foreach($result as $row)
                {
                    $jObj = json_decode($row['data']);
                    array_push($results,$jObj);
                }
                return $results;
                

            } catch (\PDOException $e) {
                exit($e->getMessage());
            }   

    }

    public function insert($data)
    {
        $statement = "INSERT INTO `tbl_order`(`data`) VALUES (:data);";
        try {
            $statement = $this->getDbConnection()->prepare($statement);
           $statement->execute(array(
                'data' => $data            
           ));
           $_SESSION['logger']->info("new order created: $data");
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
            exit($e->getMessage());
        }    
    }
    #endregion
}
