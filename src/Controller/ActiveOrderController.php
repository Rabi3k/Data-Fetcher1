<?php
namespace Src\Controller;

use Src\TableGateways\RequestsGateway;

class OrderController {

    private $db;
    private $requestMethod;
    private $orderId;

    private $requestsGateway;

    public function __construct($db, $requestMethod, $orderId=null)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->orderId = $orderId??null;

        $this->requestsGateway = new RequestsGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
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