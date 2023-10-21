<?php
namespace Src\Controller;

use Src\TableGateways\RequestsGateway;
use Src\TableGateways\OrdersGateway;

class OrderController {

    private $db;
    private $requestMethod;
    private $orderId;
    private Array $secrets;

    private RequestsGateway $requestsGateway;
    private OrdersGateway $orderGateway;

    public function __construct($db, $requestMethod, int $orderId=null, array $secrets=array())
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->orderId = $orderId??null;
        $this->secrets=$secrets;
        $this->requestsGateway = new RequestsGateway($db);
        $this->orderGateway = new OrdersGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->orderId) {
                    $response = $this->getOrder($this->orderId);
                } else {
                    $response = $this->getAllOrders([]);
                };
                break;
            case 'POST':
            case 'PUT':
                $response = $this->CompleteOrder($this->orderId);
                break;
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

    private function CompleteOrder(int $orderId)
    {
        $result = $this->orderGateway->UpdateOrderStatus($orderId,true);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(array('id'=>$orderId,'status'=>'complete','result'=>$result));
        return $response;
    }
    private function UnCompleteOrder(int $orderId)
    {
        $result = $this->orderGateway->UpdateOrderStatus($orderId,false);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(array('id'=>$orderId,'status'=>'complete','result'=>$result));
        return $response;
    }
    private function getAllOrders(array $restaurantIds)
    {
        $result = $this->orderGateway->FindByRestaurantsRefId($restaurantIds);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }
    public function getActiveOrderIds()
    {
        $sDate =(new \DateTime('midnight',new \DateTimeZone('Europe/Copenhagen')));
        $eDate =(new \DateTime('tomorrow midnight',new \DateTimeZone('Europe/Copenhagen')));
        $idOrders = $this->orderGateway->FindActiveIdsByDate($sDate,$eDate,$this->secrets);
       // $idOrders = array_column($data, 'id');
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($idOrders);
        return $response;
    }
    public function getActiveOrderIdsByDate(string $startDate,string $endDate,array $secrets)
    {
        if($sDate =\DateTime::createFromFormat('dmY',strval($startDate),new \DateTimeZone('Europe/Copenhagen')))
        {
            $sDate->setTime(0,0);
        }
        if($eDate =\DateTime::createFromFormat('dmY',strval($endDate),new \DateTimeZone('Europe/Copenhagen')))
        {
            $eDate->setTime(23,59,59,999999);
        }
        $idOrders = $this->orderGateway->FindActiveIdsByDate($sDate,$eDate,$secrets);

      
        //$idOrders = array_column($data, 'id');
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($idOrders);
        return $response;
    }
    private function getAllOrdersByDate($startDate,$endDate)
    {
        $result = $this->orderGateway->FindByDate($startDate,$endDate,$this->secrets);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }
    private function getOrder($id)
    {
        $results = $this->orderGateway->FindById($id);
       /* $result = array_filter($results,function($e) use (&$id){
            return $e["id"] === $id;
        });*/
        if (! $results) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $results->getJsonStr();
        return $response;
    }

    private function createRequestFromRequest()
    {
        
    }

    private function updateRequestFromRequest($id)
    {
        
    }

    private function deleteRequest($id)
    {
      
    }

    private function validateRequest($input)
    {
       
        return true;
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