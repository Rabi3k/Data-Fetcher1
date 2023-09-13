<?php
namespace Src\Classes;

use DateTime;
use Pinq\Queries\Requests\Values;

class Integration
{
    /*
    id int(10) UN AI PK 
restaurant_id bigint(20) UN 
gf_urid text 
loyverse_token text 
created_date timestamp
     */
    public int $Id = 0;
    public int $RestaurantId;
    public string $RestaurantName;
    public string $gfUid;
    public string $LoyverseToken;
    public string $StoreId;
    public string $TaxId;
    public DateTime $CreatedDate;
    
    public function __construct()
    {
    }

    public function LoadIntegration(array $integrationArr):Integration
    {
        $retval = new Integration();
        $retval->Id = intval($integrationArr["id"]);
        $retval->RestaurantId = intval($integrationArr["restaurant_id"]);
        $retval->RestaurantName = ($integrationArr["restaurant_name"]??"");
        $retval->gfUid = ($integrationArr["gf_urid"]);
        $retval->LoyverseToken = ($integrationArr["loyverse_token"]);
        $retval->StoreId = ($integrationArr["store_id"]);
        $retval->TaxId = ($integrationArr["tax_id"]);
        $retval->CreatedDate = DateTime::createFromFormat("Y-m-d H:i:s",$integrationArr["created_date"]);
        return $retval;
    }
    public static function GetIntegration(array $integrationArr):Integration
    {
        $retval = new Integration();
        $retval = $retval->LoadIntegration($integrationArr);
        return $retval;
    }
    public static function GetIntegrationList(array $integrationsArr):array
    {
        $retval = array();
        foreach ($integrationsArr as $key => $value) {
            # code...
            $retval[] = Integration::GetIntegration($value);
        }
        return $retval;
    }
}