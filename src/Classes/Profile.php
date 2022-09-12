<?php
namespace Src\Classes;

class Profile
{

#region Private props
    public int $id;
    public string $name;
#endregion

#region Construct
    public function __construct()
    {
    }
#endregion

#region private functions
#endregion

#region public functions
    public function LoadProfile(string $id,
        string $name)
    {
        
        $this->id = $id;
        $this->name = $name;
    }

#endregion
#region static function
    public static function GetProfile(int $id,
        string $name)
    {
        $profile = new Profile();
        $profile->LoadProfile($id,$name);
        return $profile;
    }
#endregion
}