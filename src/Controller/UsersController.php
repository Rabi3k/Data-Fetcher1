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

    public function __construct($db, $requestMethod, $params)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->params = $params;
        $this->usersGateway = new UserLoginGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                switch($this->getFunc())
                {
                    case FuncType::ByDate:
                        //pO3Mh9hwW01l
                        $password = str_Decrypt($this->params['p']);
                        $response = GeneralController::CreateResponser($this->getUser($this->params['u'],$password));
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
       if(!isset($this->params) || count($this->params)<1)
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
       if(isset($this->params['u']) && isset($this->params['p']))
       {
        return FuncType::ByDate;
       }
       return FuncType::All;
    }

    private function getUser(string $username,string $password)
    {
        $users = $this->usersGateway->GetUserByUsernamePassword($username,$password);
        if (count($users) > 0) {
            $user = $users[0];
            if (strtolower($user['user_name']) === strtolower($username) || strtolower($user['email']) === strtolower($username)) {
                return $user;
            }
        }
        /*$response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($user);*/
        return $user;
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