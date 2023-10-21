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
class ActiveOrderController
{
    #region private props
    private $db;
    private $requestMethod;
    private array $params;
    private array $secrets;
    private array $userRefIds;
    private $requestsGateway;
    private $orderGateway;
    #endregion

    #region constructor
    public function __construct($db, $requestMethod, $params = array(), $secrets = array(), $userRefIds = array())
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->params = $params;
        $this->secrets = $secrets ?? array();
        $this->userRefIds = $userRefIds ?? array();
        $this->requestsGateway = new RequestsGateway($db);
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
                        $response = $this->getActiveOrderIdsByDate($this->params['startDate'], $this->params['endDate'], $this->userRefIds);
                        break;
                    case FuncType::ById:
                        $response = $this->getOrderById($this->params['id']);
                        break;
                    case FuncType::All:
                        $response = GeneralController::CreateResponser(array());
                        break;
                    case FuncType::History:
                        $response = $this->getCompletedOrder($this->userRefIds);
                        break;
                    case FuncType::None:
                        $response = $this->notFoundResponse();
                        break;
                }
                break;
            case 'PUT':
                $body = file_get_contents('php://input');
                $input = (array) json_decode($body, TRUE);
                if (!isset($input['orderId']) || !isset($input['status'])) {
                    $response = $this->unprocessableEntityResponse();
                }
                $response = $this->ChangeOrderStatus($input['orderId'], $input['status']);
                break;
            case 'POST':
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
        if (!isset($this->params) || !isset($this->secrets) || count($this->userRefIds) < 1) {
            return FuncType::None;
        }
        if (isset($this->params["id"])) {
            return FuncType::ById;
        }
        if (isset($this->params['startDate']) && isset($this->params['endDate'])) {
            return FuncType::ByDate;
        }
        if (isset($this->params['history'])) {
            return FuncType::History;
        }
        return FuncType::All;
    }

    private function getActiveOrderIdsByDate(string $startDate, string $endDate, array $secrets)
    {
        if ($sDate = \DateTime::createFromFormat('dmY', strval($startDate), new \DateTimeZone('Europe/Copenhagen'))) {
            $sDate->setTime(0, 0);
        }
        if ($eDate = \DateTime::createFromFormat('dmY', strval($endDate), new \DateTimeZone('Europe/Copenhagen'))) {
            $eDate->setTime(23, 59, 59, 999999);
        }
        //$data = $this->orderGateway->FindActiveIdsByDate($sDate,$eDate,$secrets);
        $data = $this->orderGateway->FindActiveIdsByDateRestaurantRefId($sDate, $eDate, $secrets);
        return GeneralController::CreateResponser($data);
    }
    private function getCompletedOrder(array $secrets)
    {
        
        $data = $this->orderGateway->FindCompletedByRestaurantRefId($secrets);
        return GeneralController::CreateResponser($data);
    }
    private function getOrderById($id)
    {
        $id = \intval($id);
        $results = $this->orderGateway->FindById($id);
        /* $result = array_filter($results,function($e) use (&$id){
            return $e["id"] === $id;
        });*/
        //var_dump($results);
        if (!$results) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = GeneralController::CreateResponser($results);
        return $response;
    }

    private function ChangeOrderStatus(int $orderId, bool $status)
    {
        $result = $this->orderGateway->UpdateOrderStatus($orderId, $status);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(array('id' => $orderId, 'status' => 'complete', 'result' => $result));
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
