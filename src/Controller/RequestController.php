<?php
namespace Src\Controller;

use Src\TableGateways\RequestsGateway;
use Src\Classes\Request;

class RequestController {

    private $db;
    private $requestMethod;
    private $reqId;

    private $requestsGateway;

    public function __construct($db, $requestMethod, $reqId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->reqId = $reqId;

        $this->requestsGateway = new RequestsGateway($db);
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
                $response = $this->updateRequestFromRequest($this->reqId);
                break;
            case 'DELETE':
                $response = $this->deleteRequest($this->reqId);
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
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getRequests($id)
    {
        $result = $this->requestsGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createRequestFromRequest()
    {
        $header = json_encode(getallheaders()); 
        $body = file_get_contents('php://input');
        $input = (array) json_decode($body, TRUE);
        $results =array();
        
        foreach ($input["orders"] as $order)
        {
            $privateKey  = $order["restaurant_key"];
            $orderId  = $order["id"];
            $orderId  = $order["id"];
            $executed = $order["ready"];
            $body = json_encode($order);
            $result = $this->requestsGateway->insertFromClass(Request::GetRequest(0,
                strval($privateKey),
                 inval($orderId),
                 strval($header),
                 strval($body),
                $executed
        ));
        array_push($results,$result);
       
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
        $response['body'] = json_encode($results);
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