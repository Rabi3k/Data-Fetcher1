<?php

namespace Src\Classes;

use DateTime;
/*
CREATE TABLE `funa4298_relax_B`.`tbl_payments_relations` (
  `integration_id` INT UNSIGNED NOT NULL,
  `gf_payment` VARCHAR(45) NOT NULL,
  `loyveres_id` VARCHAR(64) NOT NULL,
  `last_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`integration_id`, `gf_payment`));
 */

class PaymentRelation
{
    public int $IntegrationId;
    public string $GfPayment;
    public string $LoyveresId;
    public DateTime $LastUpdated;

    public function __construct()
    {
    }

    public function LoadPaymentRelation(array $paymentRelation): PaymentRelation
    {
        $retval = new PaymentRelation();
        $retval->IntegrationId = intval($paymentRelation["integration_id"]);
        $retval->GfPayment = ($paymentRelation["gf_payment"]);
        $retval->LoyveresId = ($paymentRelation["loyveres_id"] ?? "");
        $retval->LastUpdated = DateTime::createFromFormat("Y-m-d H:i:s", $paymentRelation["last_updated"]);
        return $retval;
    }
    public static function GetPaymentRelation(array $integrationArr): PaymentRelation
    {
        $retval = new PaymentRelation();
        $retval = $retval->LoadPaymentRelation($integrationArr);
        return $retval;
    }
    public static function GetPaymentRelationList(array $paymentRelationArr): array
    {
        $retval = array();
        foreach ($paymentRelationArr as $key => $value) {
            # code...
            $retval[] = PaymentRelation::GetPaymentRelation($value);
        }
        return $retval;
    }
}
