<?php
namespace Src\Controller;

use Src\TableGateways\UserLoginGateway;
use Src\Enums\FuncType;
use Src\Classes\User;


class UsersController {

    private $db;
    private $requestMethod;
    private array $params;

    private $usersGateway;

    public function __construct($db, $requestMethod, $reqId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->reqId = $reqId;
        $this->usersGateway = new UserLoginGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                switch($this->getFunc())
                {
                    case FuncType::ByDate:
                        //$response = $this->getActiveOrderIdsByDate($this->params['startDate'],$this->params['endDate'],$this->secrets);
                        break;
                    case FuncType::ById:
                        //$response = $this->getOrderById($this->params['id']);
                        break;
                    case FuncType::All:
                        
                        // $response['status_code_header'] = 'HTTP/1.1 200 OK';
                        // $response['body'] = json_encode("[{'all_good':true}]");
                        break;
                    case FuncType::None:
                        $response = $this->notFoundResponse();
                        break;
                }
                break;
                break;
            case 'POST':
                //$response = $this->createRequestFromRequest();
                break;
            case 'PUT':
                //$response = $this->updateRequestFromRequest($this->reqId);
                break;
            case 'DELETE':
                //$response = $this->deleteRequest($this->reqId);
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
    private function getFunc():FuncType
    {
       if(!isset($this->params) || !isset($this->secrets) || \count($this->secrets)<1)
       {
            return FuncType::None;
       }
       if(isset($this->params["id"]))
       {
        return FuncType::ById;
       }
       if(isset($this->params["TypeId"]))
       {
        return FuncType::ById;
       }
       if(isset($this->params['startDate']) && isset($this->params['endDate']))
       {
        return FuncType::ByDate;
       }
       return FuncType::All;
    }
    #region Header response
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
#endregion
}