<?php
namespace Src\Controller;

use Src\TableGateways\RequestsGateway;

class OrderController {

    private $db;
    private $requestMethod;
    private $orderId;
    public Array $secrets;

    private $requestsGateway;

    public function __construct($db, $requestMethod, int $orderId=null, array $secrets=array())
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->orderId = $orderId??null;
        $this->secrets=$secrets;
        $this->requestsGateway = new RequestsGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->orderId) {
                    $response = $this->getOrder($this->orderId);
                } else {
                    $response = $this->getAllOrders();
                };
                break;
            case 'POST':
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

    private function getAllOrders()
    {
        $result = $this->requestsGateway->RetriveAllOrders();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }
    public function getActiveOrderIds()
    {
        $sDate =(new \DateTime('midnight',new \DateTimeZone('Europe/Copenhagen')));
        $eDate =(new \DateTime('tomorrow midnight',new \DateTimeZone('Europe/Copenhagen')));
        $data = $this->requestsGateway->RetriveAllOrdersByDate($sDate,$eDate);
        $idOrders = array_column($data, 'id');
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
        $data = $this->requestsGateway->RetriveAllOrdersByDate($sDate,$eDate,$secrets);

      
        $idOrders = array_column($found_orders, 'id');
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($idOrders);
        return $response;
    }
    private function getAllOrdersByDate($startDate,$endDate)
    {
        $result = $this->requestsGateway->RetriveAllOrdersByDate($startDate,$endDate);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }
    private function getOrder($id)
    {
        $results = $this->requestsGateway->RetriveOrder($id);
       /* $result = array_filter($results,function($e) use (&$id){
            return $e["id"] === $id;
        });*/
        if (! $results) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($results);
        return $response;
    }

    private function createRequestFromRequest()
    {
        $body = file_get_contents('php://input');
        $input = (array) json_decode($body, TRUE);
        foreach ($input["orders"] as $order)
        {
            $privateKey  = $order["restaurant_key"];
            $body = json_encode($order);
            $result = $this->requestsGateway->insert([
                'private_key' => $privateKey,
                'body' => $body
        ]);
        }

       // $privateKey  = $input["restaurant_key"];



        /*if (! $this->validateRequest($input)) {
            return $this->unprocessableEntityResponse();
        }*/
       /* $result = $this->requestsGateway->insert([
            'private_key' => $privateKey,
            'body' => $body
        ]);*/
        //$this->requestsGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateRequestFromRequest($id)
    {
        $result = $this->requestsGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateRequest($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->requestsGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteRequest($id)
    {
        $result = $this->requestsGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->requestsGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateRequest($input)
    {
        if (! isset($input['private_key'])) {
            return false;
        }
        if (! isset($input['body'])) {
            return false;
        }
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