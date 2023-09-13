<?php

namespace Src\Controller;

use Src\Classes\Integration;
use Src\TableGateways\RequestsGateway;
use Src\TableGateways\OrdersGateway;
use Src\Classes\Request;

class RequestController
{

    private $db;
    private $requestMethod;
    private $reqId;

    private $requestsGateway;
    private $ordersGateway;

    public function __construct($db, $requestMethod, $reqId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->reqId = $reqId;

        $this->requestsGateway = new RequestsGateway($db);
        $this->ordersGateway = new OrdersGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->reqId) {
                    $response = $this->getRequests($this->reqId);
                } else {
                    $response = $this->getAllRequests();
                };
                break;
            case 'POST':
                $response = $this->createRequestFromRequest();
                break;
            case 'PUT':
                break;
            case 'DELETE':
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllRequests()
    {
        $result = $this->requestsGateway->findAll();
        // $response['status_code_header'] = http_response_code();//'HTTP/1.1 200 OK';
        // $response['body'] = json_encode($result);
        return GeneralController::CreateResponser($result);
    }

    private function getRequests($id)
    {
        $result = $this->requestsGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        return GeneralController::CreateResponser($result);
    }

    private function createRequestFromRequest()
    {
        $header = json_encode(getallheaders());
        $body = file_get_contents('php://input');
        $input = (array) json_decode($body, TRUE);
        $results = array();
        $req = Request::GetRequest(
            strval($header),
            strval($body),
            ""
        );
        $result = $this->requestsGateway->insertFromClass($req);
        //array_push($results, $result);
        foreach ($input["orders"] as $order) {

            $restaurant_refId  = $order["restaurant_id"];
            $order_id  = $order["id"];
            $body = json_encode($order);
            $result =$this->ordersGateway->InsertOrUpdate($body,$order_id,$restaurant_refId);
            $results[$order["id"]] = $result;
            $order = $this->ordersGateway->FindById($order_id);
            //post order to LPos
            IntegrationController::PostOrder($order);
            //save posted order to posted type
        }

        return GeneralController::CreateResponser($results);
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
