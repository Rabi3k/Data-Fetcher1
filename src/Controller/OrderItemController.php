<?php

namespace Src\Controller;


use Src\TableGateways\OrdersGateway;
use Src\Enums\FuncType;
use Src\Classes\User;
use Src\TableGateways\OrderItemGateway;

// enum FuncType
// {
//     case ByDate;
//     case ById;
//     case All;
//     case None;
// }
class OrderItemController
{
    private $db;
    private $requestMethod;
    private array $params;
    private array $secrets;
    private $OrderItemGateway;
    private $orderGateway;
    #endregion

    #region constructor
    public function __construct($db, $requestMethod, $params = array(), $secrets = array())
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->params = $params;
        $this->secrets = $secrets ?? array();
        $this->OrderItemGateway = new OrderItemGateway($db);
        $this->orderGateway = new OrdersGateway($db);
    }
    #endregion

    #region request process
    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                switch ($this->getFunc()) {
                    case FuncType::ByDate:
                    case FuncType::ById:
                    case FuncType::All:
                        // $response['status_code_header'] = 'HTTP/1.1 200 OK';
                        // $response['body'] = json_encode("[{'all_good':true}]");
                        // break;
                    case FuncType::None:
                        $response = $this->notFoundResponse();
                        break;
                }
                break;
            case 'POST':
            case 'PUT':
                $response = $this->UpdateOrderItemStatus();
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
    #endregion

    #region private function
    private function getFunc(): FuncType
    {
        if (!isset($this->params) || !isset($this->secrets) || \count($this->secrets) < 1) {
            return FuncType::None;
        }
        if (isset($this->params["id"])) {
            return FuncType::ById;
        }
        if (isset($this->params['startDate']) && isset($this->params['endDate'])) {
            return FuncType::ByDate;
        }
        return FuncType::All;
    }

    private function UpdateOrderItemStatus()
    {
        $body = file_get_contents('php://input');
        $input = json_decode($body);
        $this->OrderItemGateway->UpdateOrderItemStatus($input->id,$input->status);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';

        $response['body'] = json_encode(array('id'=>$input->id,'status'=>'$input->status'));
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
