<?php
namespace Src\Classes;

class User{
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
#endregion

#region Construct
    public function __construct()
    {
        $this->profile=new Profile();
        $this->restaurants=array();
        $this->secrets=array();
        
    }
#endregion

#region private functions
#endregion

#region public functions
    public function LoadUser(string $id,
        string $email,
        string $user_name,
        string $full_name,
        string $password,
        string $secret_key,
        string $profile,
        array $secrets,
        array $restaurants)
    {
        
        $this->id = $id;
        $this->email = $email;
        $this->user_name = $user_name;
        $this->full_name = $full_name;
        $this->password = $password;
        $this->secret_key = $secret_key;
        $this->profile=$profile;
        $this->restaurants=$restaurants; 
        $this->restaurants=$secrets;
    }

#endregion
#region static function
    public static function GetUser(int $id,
        string $email,
        string $user_name,
        string $full_name,
        string $password,
        string $secret_key,
        Profile $profile,
        array $secrets,
        array $restaurants)
    {
        $user = new User();
        $user->id = $id;
        $user->email = $email;
        $user->user_name = $user_name;
        $user->full_name = $full_name;
        $user->password = $password;
        $user->secret_key = $secret_key;
        $user->profile=$profile;
        $user->restaurants=$restaurants;
        $user->restaurants=$secrets;
        return $user;
    }
#endregion
}