<?php
namespace Src\Controller;

use Src\TableGateways\UserLoginGateway;
use Src\Enums\FuncType;
use Src\Classes\User;
use Src\TableGateways\UserGateway;

class UsersController {

    private $db;
    private $requestMethod;
    private array $params;

    private $usersGateway;
    private $userGateway;

    public function __construct($db, $requestMethod, $params)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->params = $params;
        //$this->usersGateway = new UserLoginGateway($db);
        $this->userGateway = new UserGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                switch($this->getFunc())
                {
                    case ProcessType::UserPassword:
                        //pO3Mh9hwW01l
                        $password = str_Decrypt($this->params['p']);
                        //echo $password;
                        $response = GeneralController::CreateResponser($this->getUser($this->params['u'],$password));
                        break;
                    case ProcessType::ByID:
                        $response = GeneralController::CreateResponser($this->getUserById($this->params['id']));
                        //$response = $this->getOrderById($this->params['id']);
                        break;
                    case ProcessType::Passkey:
                        $response = GeneralController::CreateResponser($this->getUserByPasskey($this->params['p']));
                        break;
                    case ProcessType::All:
                        
                         $response['status_code_header'] = 'HTTP/1.1 200 OK';
                         $response['body'] = json_encode("[{'all_good':true}]");
                        break;
                    case ProcessType::None:
                        default:
                        $response = $this->notFoundResponse();
                        break;
                }
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
    
    private function getFunc():ProcessType
    {
       if(!isset($this->params) || count($this->params)<1)
       {
            return ProcessType::None;
       }
       if(isset($this->params["id"]))
       {
        return ProcessType::ByID;
       }
       if(isset($this->params["TypeId"]))
       {
        return ProcessType::ByID;
       }
       if(isset($this->params['u']) && isset($this->params['p']))
       {
        return ProcessType::UserPassword;
       }
       if(!isset($this->params['u']) && isset($this->params['p']))
       {
        return ProcessType::Passkey;
       }
       return ProcessType::All;
    }

    private function getUserById($id)
    {
        $u= $this->userGateway->FindById($id);
        return $u->getJson();
    }
    private function getUserByPasskey($passkey)
    {
        $u= $this->userGateway->getUserByPasskey(urldecode($passkey));
        return $u->getJson();
    }
    private function getUser(string $username,string $password)
    {
        $users = $this->userGateway->GetUserByUsernamePassword($username,$password);
        if (isset($users)) {
            //$user = $users[0];
            $user  = UserGateway::GetUserClass($users->id);

            if (strtolower($user->user_name) === strtolower($username) || strtolower($user->email) === strtolower($username)) {
                return $user->getJson();
            }
        }
        /*$response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($user);*/
        return array();
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

enum ProcessType {
    case ByID;
    case UserPassword;
    case Passkey;
    case None;
    case All;
}