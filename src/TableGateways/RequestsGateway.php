<?php
namespace Src\TableGateways;
use DateTime;
use Src\Classes\Request;
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
        $statement = "SELECT 
            Max(id) as 'id',
            Max(private_key) as 'private_key',
            Max(order_id) as 'order_id',
            Max(body) as 'body',
            Max(created_date) as 'created_date',
            Max(executed) as 'executed'
        FROM $this->tblName group by order_id having (executed)=0;";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
           // echo \json_encode($result);

            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function RetriveAllOrders()
    {
       $result = $this->findAllActive();
       $orders = array();
       //echo "<span class='card'>".\json_encode($result)."<br/></span>";
        try {
            foreach($result as $request)
            {
                $order = json_decode($request["body"],true);
                //echo "<span class='card'>".\json_encode($order)."<br/></span>";
                $found_key = array_filter($orders,function($e) use (&$order){
                   // echo "<span class='card'>".\json_encode($order)."<br/></span>";
                    return $e["id"] === $order["id"] || $order["status"] !=='accepted';
                });
                if(!$found_key)
                {
                    //echo "<span class='card'>".\json_encode($order)."<br/></span>";
                    array_push($orders,$order);
                }
            }
            return  $orders;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function RetriveAllOrdersByDate($startDate,$endDate,$secrets)
    {
        try 
        {
            
            $sDate =($startDate);
           $eDate =($endDate);
           
            $orders = $this->RetriveAllOrders();
           
            $found_orders = array_filter($orders,function($e) use (&$sDate,&$eDate,&$secrets){
                $oDate = new \DateTime($e["fulfill_at"]);
                $oDate->setTimezone( new \DateTimeZone($e["restaurant_timezone"]));
                //echo "<span class='card'>".$sDate->format('m-d H:i') ."=>". $oDate->format('m-d H:i') ."&&". $eDate->format('m-d H:i') ."=>". $oDate->format('m-d H:i')."<br/></span>";
               // echo "<span class='card'>".."<br/></span>";
                return ($sDate <= $oDate && $eDate >=$oDate && in_array(strval($e["restaurant_key"]),$secrets) );
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
                array_push($orders,$order);
            }
            return  $orders;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function compare_func($a, $b)
{
    $oDate = new \DateTime($a["updated_at"]);
    $oDate->setTimezone( new \DateTimeZone($a["restaurant_timezone"]));
    $oDate2 = new \DateTime($b["updated_at"]);
    $oDate2->setTimezone( new \DateTimeZone($b["restaurant_timezone"]));
    if ($oDate == $oDate2) {
        return 0;
    }
    return ($oDate < $oDate2) ? -1 : 1;
    // You can apply your own sorting logic here.
}

//usort($arrayOfObjects, "compare_func");

    public function RetriveLastOrderById($id)
    {
        
        try {
            $result = $this->RetriveOrder($id);
            if(\count($result)>1)
            {
                $order =usort($result, function($a, $b)
                {
                    $oDate = new \DateTime($a["updated_at"]);
                    $oDate->setTimezone( new \DateTimeZone($a["restaurant_timezone"]));
                    $oDate2 = new \DateTime($b["updated_at"]);
                    $oDate2->setTimezone( new \DateTimeZone($b["restaurant_timezone"]));
                    if ($oDate == $oDate2) {
                        return 0;
                    }
                    return ($oDate < $oDate2);
                    // You can apply your own sorting logic here.
                });
            }
            return (isset($result) && count($result)>0)? $result[0]:NULL;
            
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
    public function insertFromClass(Request $input)
    {
        $statement = "INSERT INTO $this->tblName
        (`private_key`, `order_id`, `header`, `body`, `executed`) 
        VALUES 
        (:private_key, :order_id, :header, :body, :executed);";
/*
    public $id; //int
    public $private_key; //String
    public $order_id; //int
    public $header; //String
    public $body; //String
    public $created_date; //Date
    public $executed; //int
   
 */
        try {
            $statement = $this->db->prepare($statement);
           $statement->execute(array(
                'private_key' => $input->private_key,
                'order_id'  => $input->order_id,
                'header'  => $input->header,
                'body'  => $input->body,
                'executed'  =>$input->executed,
           ));
            
                $oDate = new \DateTime();
                $oDate->setTimezone( new \DateTimeZone('Europe/Copenhagen'));
                if (!file_exists("logs/".$oDate->format('dmY'))) {
                    mkdir("logs/".$oDate->format('dmY'), 0777, true);
                }
                $myfile = fopen("logs/".$oDate->format('dmY')."/Log_".$input->order_id.".txt", "w")
                                or die("Unable to open file!");
                $inputStr ="Test 123123:".json_encode($input)."\n\r SqlStatment:".json_encode($statement);
                fwrite($myfile, $inputStr);
                fclose($myfile);
            
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function insert(Array $input)
    {
        $statement = "INSERT INTO $this->tblName
        (`private_key`, `order_id`, `header`, `body`, `executed`) 
        VALUES 
        (:private_key, :order_id, :header, :body, :executed);";

        try {
            $statement = $this->db->prepare($statement);
           if(! $statement->execute(array(
                'private_key' => $input['private_key'],
                'order_id'  => $input['order_id'],
                'header'  => $input['header'],
                'body'  => $input['body'],
                'executed'  =>$input['executed'],
            )))
            {
                $oDate = new \DateTime();
                $oDate->setTimezone( new \DateTimeZone('Europe/Copenhagen'));
                if (!file_exists("logs/".$oDate->format('dmY'))) {
                    mkdir("logs/".$oDate->format('dmY'), 0777, true);
                }
                $myfile = fopen("logs/".$oDate->format('dmY')."/Log_".$input['order_id'].".txt", "w")
                                or die("Unable to open file!");
                $inputStr ="Test 123123:".json_decode($input);
                fwrite($myfile, $inputStr);
                fclose($myfile);
            };
            
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