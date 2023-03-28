<?php

namespace Src\Classes;

use Src\Classes\ClassObj;
use Src\Enums\ScreenType;
use Pinq\Traversable;

/*
 * this Class represents the company 
 * @Table("tbl_companies") 
 * 
 */

class LoginUser extends ClassObj
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

    public function UserBranches(): array
    {
        return (Traversable::from($this->restaurants))->select(function ($x) {
            return $x;
        })->asArray();
    }
    public function GetScreenType(): string
    {
        return strtolower(ScreenType::from($this->screen_type)->name);
    }
    public function GetScreenTypeText(): string
    {
        switch ($this->screen_type) {
            case ScreenType::ODS:
                return "Order Panel";
            case ScreenType::IDS:
                return "Item Panel";
            case ScreenType::CDS:
                return "Customer Panel";
            default:
                return "Order Panel";
        }
    }
    public function LoadUser($companyObj)
    {

        $this->LoadDataObject($companyObj);
    }

    #endregion
    #region static function
    public static function NewUser(): LoginUser
    {
        $c = new LoginUser();
        $c->setFromJsonStr('
        {
            "id": 1,
            "email": "",
            "full_name": "",
            "user_name": "",
            "password": "",
            "secret_key": "",
            "screen_type": 1,
            "isSuperAdmin": 0,
            "IsAdmin": 0,
            "profile_id": 0,
            "Profile": null,
            "companies": [],
            "restaurants": [],
            "Restaurants_Id": []
        }');
        return $c;
    }
    public static function GetUserFromJsonStr($userObj)
    {
        $c = new LoginUser();
        $c->setFromJsonStr($userObj);
        return $c;
    }
    public static function GetUser($userObj)
    {
        $c = new LoginUser();
        $c->LoadUser($userObj);
        return $c;
    }
    public static function GetUserList(array $users)
    {
        $retval = array();
        foreach ($users as $c) {
            $retval[] = new LoginUser($c);
        }
        return $retval;
    }
    #endregion
}