<?php
namespace Src\Classes;

class Restaurant
{
#region public props
    public int $id;
    public string $email;
    public string $name;
    public string $phone;
    public string $cvr;
    public string $logo;
    public string $reference_id;
    public array $branches;
#endregion

#region Construct
    public function __construct()
    {
        $this->branches=array();
    }
#endregion

#region private functions
#endregion

#region public functions
    public function LoadRestaurant(int $id,
        string $email,
        string $name,
        string $phone,
        string $cvr,
        string $logo,
        string $reference_id,
        array $branches)
    {
        
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->phone = $phone;
        $this->cvr = $cvr;
        $this->logo = $logo;
        $this->reference_id = $reference_id;
        $this->branches=$branches;
        $this->restaurant=$restaurant;
    }

#endregion
#region static function

    public static function GetRestaurant(int $id,
        string $email,
        string $name,
        string $phone,
        string $cvr,
        string $logo,
        string $reference_id,
        array $branches)
    {
        $restaurant = new Restaurant();
        $restaurant->id = $id;
        $restaurant->email = $email;
        $restaurant->name = $name;
        $restaurant->phone = $phone;
        $restaurant->cvr = $cvr;
        $restaurant->logo = $logo;
        $restaurant->reference_id = $reference_id;
        $restaurant->branches=$branches;
        return $restaurant;
    }
#endregion

}