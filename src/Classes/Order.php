<?php
namespace Src\Classes;
use Src\Classes\ClassObj;

class Order extends ClassObj
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
    public function LoadOrder($orderObj)
    {
        
        $this->LoadDataObject($orderObj);
    }
  
#endregion
#region static function
    public static function GetOrder($orderObj)
    {
        $order = new Order();
        $order->LoadOrder($orderObj);
        return $order;
    }
    public static function GetOrderList(array $orders)
    {
        $retval =array();
        foreach ($orders as $o) {
            $retval []= new Order($o);
        }
        return $retval;
    }
#endregion
}