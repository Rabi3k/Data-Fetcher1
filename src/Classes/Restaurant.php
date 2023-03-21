<?php
namespace Src\Classes;
use Src\Classes\ClassObj;

class Restaurant extends ClassObj
{

#region Private props
#endregion

#region Construct
    public function __construct($data =new \stdClass())
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
    public function LoadRestaurant($restaurantObj)
    {
        
        $this->LoadDataObject($restaurantObj);
    }
  
#endregion
#region static function
    public static function GetRestaurant($restaurantObj)
    {
        $restaurant = new Restaurant();
        $restaurant->LoadRestaurant($restaurantObj);
        return $restaurant;
    }
    public static function GetRestaurantList(array $restaurants)
    {
        $retval =array();
        foreach ($restaurants as $c) {
            $retval []= new Restaurant($c);
        }
        return $retval;
    }
    public static function NewRestaurant()
    {
        $r = new Restaurant();
        $r->setFromJsonStr('{
            "id": 0,
            "city": "",
            "name": "",
            "p_nr": "",
            "alias": "",
            "email": "",
            "phone": null,
            "post_nr": null,
            "address": null,
            "country": null,
            "is_gf": false,
            "gf_urid": null,
            "gf_refid": null,
            "company_id": null,
            "is_managed": null,
            "gf_cdn_base_path": null
        }');
        return $r;
    }
#endregion
}