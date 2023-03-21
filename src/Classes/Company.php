<?php

namespace Src\Classes;

use Src\Classes\ClassObj;
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
        $company->LoadCompany($companyObj);
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
    #endregion
}
