<?php

namespace Src\Classes;

class User
{
    /*  `id`, `email`, `user_name`, `full_name`, `password`, `secret_key`, `profile_id`, `restaurant_id` */
    #region public props
    public int $id;
    public string $email;
    public string $user_name;
    public string $full_name;
    public string $password;
    public string $secret_key;
    public Profile $profile;
    public array $restaurants;
    public array $secrets;
    public bool $isAdmin, $isSuperAdmin;
    public function usertype()
    {
        if ($this->isSuperAdmin) {
            return "SuperAdmin";
        } else if ($this->isAdmin) {
            return "Admin";
        } else {
            return "User";
        }
    }
    public function SetUsertype(string $type)
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
    #endregion

    #region Construct
    public function __construct()
    {
        $this->id = 0;
        $this->email = "";
        $this->user_name = "";
        $this->full_name = "";
        $this->password = "";
        $this->secret_key = "";
        $this->isAdmin = false;
        $this->isSuperAdmin = false;
        $this->profile = new Profile();
        $this->restaurants = array();
        $this->secrets = array();
    }
    #endregion

    #region private functions
    #endregion

    #region public functions
    public function LoadUser(
        string $id,
        string $email,
        string $user_name,
        string $full_name,
        string $password,
        string $secret_key,
        Profile $profile,
        array $secrets,
        array $restaurants,
        bool $isAdmin,
        bool $isSuperAdmin
    ) {

        $this->id = $id;
        $this->email = $email;
        $this->user_name = $user_name;
        $this->full_name = $full_name;
        $this->password = $password;
        $this->secret_key = $secret_key;
        $this->profile = $profile;
        $this->restaurants = $restaurants;
        $this->secrets = $secrets;
        $this->isAdmin = $isAdmin;
        $this->isSuperAdmin = $isSuperAdmin;
    }

    #endregion
    #region static function
    public static function GetUser(
        int $id,
        string $email,
        string $user_name,
        string $full_name,
        string $password,
        string $secret_key,
        Profile $profile,
        array $secrets,
        array $restaurants,
        bool $isAdmin,
        bool $isSuperAdmin
    ) {
        $user = new User();
        $user->LoadUser(
            $id,
            $email,
            $user_name,
            $full_name,
            $password,
            $secret_key,
            $profile,
            $secrets,
            $restaurants,
            $isAdmin,
            $isSuperAdmin
        );

        return $user;
    }
    #endregion
}
