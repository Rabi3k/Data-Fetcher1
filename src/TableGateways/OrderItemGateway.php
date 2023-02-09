<?php

namespace Src\TableGateways;

use Pinq\Traversable;
use Src\Classes\Loggy;
use Src\Classes\Order;
use Src\Enums\ItemStatus;
use Src\System\DbObject;

class OrderItemGateway extends DbObject
{

    #region abstract functions
    protected function SetSelectStatment()
    {
        $tblname = $this->getTableName();
        $this->selectStatment = "SELECT * FROM $tblname;";
    }
    protected function SetTableName()
    {
        $this->tblName = "order_items";
    }
    public function UpdateOrderItemStatus(int $orderItemId, string $status)
    {
        $statment = "UPDATE `kbslb_portal`.`order_items`
        SET
        `status` = :status
        WHERE 
        `id` = :id;
        ";
        try {
            $statment = $this->getDbConnection()->prepare($statment);
            $this->getDbConnection()->beginTransaction();
            $statment->execute(array(
                'id' => intval($orderItemId),
                'status' => $status,
            ));
            $statment->closeCursor();

            $this->getDbConnection()->commit();
            return $statment->rowCount();
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
}