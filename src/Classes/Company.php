<?php

namespace Src\Classes;

use Pinq\Traversable;
use Src\Classes\ClassObj;
use Src\TableGateways\CompanyGateway;
use Src\TableGateways\RestaurantsGateway;

/*
 * this Class represents the company 
 * @Table("tbl_companies") 
 * 
 */

class Company extends ClassObj
{

    #region Private props
    #endregion

    #region Construct
    public function __construct($data = new \stdClass())
    {
        $this->LoadDataObject($data);
    }
    #endregion

    #region private functions
    #endregion

    #region protected functions
    protected function LoadDataObject($data)
    {
        $this->data = $data;
    }
    #endregion

    #region public functions
    public function LoadCompany($companyObj)
    {

        $this->LoadDataObject($companyObj);
    }

    #endregion
    #region static function
    public static function NewCompany(): Company
    {
        $c = new Company();
        $c->setFromJsonStr('
                {
                "id": 0,
                "name": "",
                "cvr_nr": "",
                "address": "",
                "zip": "",
                "city": "",
                "email": "",
                "phone": "",
                "gf_refid": ""
                }
            ');
        return $c;
    }
    public static function GetCompany($companyObj)
    {
        $company = new Company();
        $company->setFromJsonStr($companyObj);
        return $company;
    }
    public static function GetCompanyList(array $companies)
    {
        $retval = array();
        foreach ($companies as $c) {
            $retval[] = new Company($c);
        }
        return $retval;
    }
    public static function GetCompaniesJsonList(array $companies)
    {
        $retval = array();
        foreach ($companies as $c) {
            $retval[] = Company::GetCompany($c);
        }
        return $retval;
    }
    public static function getAllCompaniesTree()
    {
        global $dbConnection;
        
        $restaurants = (new RestaurantsGateway($dbConnection))->GetAll();
        $companies = (new CompanyGateway($dbConnection))->GetAll();

        $retval = array();
            foreach ($companies as $c) {

                $cRest =  (Traversable::from($restaurants))->where(function ($r) use ($c) {
                    return $r->company_id === $c->id;
                })->asArray();
                $c->restaurants = new Restaurant($cRest);
                $retval[] = new Company($c);
            }

        return $retval;

    }
    
    #endregion
}
