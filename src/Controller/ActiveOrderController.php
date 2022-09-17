<?php
namespace Src\Controller;

use Src\TableGateways\RequestsGateway;
use Src\TableGateways\OrdersGateway;
use Src\Enums\FuncType;
use Src\Classes\User;
// enum FuncType
// {
//     case ByDate;
//     case ById;
//     case All;
//     case None;
// }
class ActiveOrderController {
#region private props
    private $db;
    private $requestMethod;
    private array $params;
    private array $secrets;
    private $requestsGateway;
#endregion

#region constructor
    public function __construct($db, $requestMethod,$params =array(),$secrets = array())
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->params = $params;
        $this->secrets = $secrets??array();

        $this->requestsGateway = new RequestsGateway($db);
        $this->orderGateway = new OrdersGateway($db);
    }
#endregion

#region request process
    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                switch($this->getFunc())
                {
                    case FuncType::ByDate:
                        $response = $this->getActiveOrderIdsByDate($this->params['startDate'],$this->params['endDate'],$this->secrets);
                        break;
                    case FuncType::ById:
                        $response = $this->getOrderById($this->params['id']);
                        break;
                    case FuncType::All:
                        $response['status_code_header'] = 'HTTP/1.1 200 OK';
                        $response['body'] = json_encode("[{'all_good':true}]");
                        break;
                    case FuncType::None:
                        $response = $this->notFoundResponse();
                        break;
                }
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
#endregion

#region private function
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
       if(isset($this->params['startDate']) && isset($this->params['endDate']))
       {
        return FuncType::ByDate;
       }
       return FuncType::All;
    }
   
    private function getActiveOrderIdsByDate(string $startDate,string $endDate,array $secrets)
    {
        if($sDate =\DateTime::createFromFormat('dmY',strval($startDate),new \DateTimeZone('Europe/Copenhagen')))
        {
            $sDate->setTime(0,0);
        }
        if($eDate =\DateTime::createFromFormat('dmY',strval($endDate),new \DateTimeZone('Europe/Copenhagen')))
        {
            $eDate->setTime(23,59,59,999999);
        }
        $data = $this->orderGateway->FindActiveIdsByDate($sDate,$eDate,$secrets);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($data);
        return $response;
    }
    private function getOrderById($id)
    {
        $id = \intval($id);
        $results = $this->orderGateway->FindById($id);
       /* $result = array_filter($results,function($e) use (&$id){
            return $e["id"] === $id;
        });*/
        //var_dump($results);
        if (! $results) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($results);
        return $response;
    }
#endregion

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