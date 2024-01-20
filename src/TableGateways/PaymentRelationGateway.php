<?php

namespace Src\TableGateways;

use Src\Classes\Loggy;
use Src\Classes\PaymentRelation;
use Src\System\DbObject;

class PaymentRelationGateway extends DbObject
{
    #region protected functions
    protected function SetSelectStatment()
    {
        $this->selectStatment = "SELECT * FROM $this->tblName";
    }
    protected function SetTableName()
    {
        $this->tblName = "`tbl_payments_relations`";
    }
    #endregion
    public function findByKey(int $integrationId, string $gfPayment): PaymentRelation|Null
    {
        $statment = "$this->selectStatment Where integration_id = $integrationId AND gf_payment = '$gfPayment';";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            return PaymentRelation::GetPaymentRelation($result[0]);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function findByIntegrationId(int $integrationId): array
    {
        $statment = "$this->selectStatment Where integration_id = $integrationId;";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return array();
            }
            return PaymentRelation::GetPaymentRelationList($result);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function InsertOrupdate(PaymentRelation $input)
    {
        $statement = "INSERT INTO $this->tblName
        (`integration_id`,
        `gf_payment`,
        `loyveres_id`)
        VALUES
        (:integration_id,
        :gf_payment,
        :loyveres_id)
         ON DUPLICATE KEY UPDATE
         `loyveres_id` = :loyveres_id
         ;";

        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'integration_id' => $input->IntegrationId,
                'gf_payment' => $input->GfPayment,
                'loyveres_id' => $input->LoyveresId,
            ));
            $this->getDbConnection()->commit();
            return $input;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
}
