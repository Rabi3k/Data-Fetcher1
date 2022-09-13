<?php
namespace Src\Classes;

class Request
{
    public $id; //int
    public $private_key; //String
    public $order_id; //int
    public $header; //String
    public $body; //String
    public $created_date; //Date
    public $executed; //int
   


    #region public functions
    public function LoadRequest(int $id,
        string $private_key,
        string $order_id,
        string $header,
        string $body,
        string $created_date,
        array $executed)
    {
        
        $this->id = $id;
        $this->private_key = $private_key;
        $this->order_id = $order_id;
        $this->header = $header;
        $this->body = $body;
        $this->created_date = $created_date;
        $this->reference_id = $reference_id;
        $this->executed=$executed;
    }

#endregion
#region static function

    public static function GetRequest(int $id,
    string $private_key,
    string $order_id,
    string $header,
    string $body,
    string $created_date,
    array $executed)
    {
        $request = new Request();
        $restaurant->LoadRequest($id,
         $private_key,
         $order_id,
         $header,
         $body,
         $created_date,
         $executed);
         //echo json_encode($restaurant);
        return $request;
    }
#endregion
   }