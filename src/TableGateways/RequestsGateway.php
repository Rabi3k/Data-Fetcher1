<?php
namespace Src\TableGateways;
use DateTime;
class RequestsGateway {

    private $db = null;
    private $tblName = "`requests`";

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "SELECT * FROM $this->tblName;";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function findAllActive()
    {
        $statement = "SELECT * FROM $this->tblName group by `order_id` HAVING MAX(executed)=0;";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function RetriveAllOrders()
    {
       $result = $this->findAllActive();
       $orders = array();
        try {
            foreach($result as $request)
            {
                $order = json_decode($request["body"],true);
                $found_key = array_filter($orders,function($e) use (&$order){
                    return $e["id"] === $order["id"];
                });
                if(!$found_key)
                {
                    array_push($orders,$order);
                }
            }
            return  $orders;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function RetriveAllOrdersByDate($startDate,$endDate)
    {
        try 
        {
            
            $sDate =($startDate);
           $eDate =($endDate);
           
            $orders = $this->RetriveAllOrders();
            $found_orders = array_filter($orders,function($e) use (&$sDate,&$eDate){
                $oDate = new \DateTime($e["fulfill_at"]);
                $oDate->setTimezone( new \DateTimeZone($e["restaurant_timezone"]));
               // echo "<span class='card'>".$sDate->format('m-d h:i') ."=>". $oDate->format('m-d h:i') ."&&". $eDate->format('m-d h:i') ."=>". $oDate->format('m-d h:i')."<br/></span>";
                return ($sDate <= $oDate && $eDate >=$oDate);
                });
            return  $found_orders;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function RetriveOrder($id)
    {
       $result = $this->findByOrderId($id);
       $orders = array();
        try {
            foreach($result as $request)
            {
                $order = json_decode($request["body"],true);
                $found_key = array_filter($orders,function($e) use (&$order){
                    return $e["id"] === $order["id"];
                });
                
                    array_push($orders,$order);
                
            }
            return  $orders;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function find($id)
    {
        $statement = "SELECT * FROM $this->tblName WHERE id = ?;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
    public function findByOrderId($id)
    {
        $statement = "SELECT * FROM $this->tblName WHERE order_id = ?;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }


    public function insert(Array $input)
    {
        $statement = "INSERT INTO $this->tblName
        (`private_key`, `body`,`order_id`, `executed`) 
        VALUES 
        (:private_key, :body, :order_id, :executed);";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'private_key' => $input['private_key'],
                'body'  => $input['body'],
                'order_id'  => $input['order_id'],
                'executed'  =>$input['executed'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        $statement = "
            UPDATE $this->tblName
            SET 
            private_key = :private_key,
            order_id = :order_id,
            body  = :body,
            created_date = :created_date,
            executed = :executed
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'private_key' => $input['private_key'],
                'order_id' => $input['order_id'] ?? null,
                'body'  => $input['body'],
                'created_date' => $input['created_date'] ?? null,
                'executed' => $input['executed'] ?? null,
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM $this->tblName
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
}