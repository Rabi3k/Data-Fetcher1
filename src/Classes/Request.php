<?php
namespace Src\Classes;

class Request
{
    public $header; //String
    public $body; //String
    public $created_date; //Date
   


    #region public functions
    public function LoadRequest(
        string $header,
        string $body,
        string $created_date
        )
    {
        
        $this->header = $header;
        $this->body = $body;
        $this->created_date = $created_date;
    }

#endregion
#region static function

    public static function GetRequest(
    string $header,
    string $body,
    string $created_date)
    {
        $request = new Request();
        $request->LoadRequest(
         $header,
         $body,
         $created_date);
         //echo json_encode($restaurant);
        return $request;
    }
#endregion
   }