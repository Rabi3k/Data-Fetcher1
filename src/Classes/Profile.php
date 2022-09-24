<?php

namespace Src\Classes;

class Profile
{

    #region Private props
    public int $id = 0;
    public string $name = '';
    public bool $isSuperAdmin = false;
    public bool $isAdmin = false;
    #endregion

    #region Construct
    public function __construct()
    {
    }
    #endregion

    #region private functions
    #endregion

    #region public functions
    public function GetProfileType():string
    {
        $retval="User";
        $retval= $this->isSuperAdmin?"SuperAdmin":$retval;
        $retval= $this->isAdmin?"Admin":$retval;

        return $retval;
    }
    public function SetProfileType(string $type)
    {
        switch (strtolower($type)) {
            case "superadmin":
                $this->isSuperAdmin = true;
                $this->isAdmin = false;
                return;
            case "admin":
                $this->isSuperAdmin = false;
                $this->isAdmin = true;
                return;
            default:
                $this->isSuperAdmin =  $this->isAdmin = false;
                return;
        }
    }
    public function LoadProfile(
        string $id,
        string $name,
        bool $isSuperAdmin = false,
        bool $isAdmin = false
    ) {

        $this->id = $id;
        $this->name = $name;
        $this->isSuperAdmin = $isSuperAdmin;
        $this->isAdmin = $isAdmin;
    }

    #endregion
    #region static function
    public static function GetProfile(
        int $id,
        string $name,
        bool $isSuperAdmin = false,
        bool $isAdmin = false
    ) {
        $profile = new Profile();
        $profile->LoadProfile(
            $id,
            $name,
            $isSuperAdmin,
            $isAdmin
        );
        return $profile;
    }
    #endregion
}
