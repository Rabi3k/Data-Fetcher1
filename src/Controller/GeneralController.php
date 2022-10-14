<?php
namespace Src\Controller;

use DateTime;

class GeneralController
{
    public static function CreateResponser($data)
    {
        $responser = new Responser($data);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] =json_encode($responser);
        return $response;
    }
}
class Responser {
    public string $id ;
    public string $apiVersion ;
    public string $context ;
    public string $servedBy ;
    public DateTime $timeServed ;
    public string $appVersion ;
    public string $took ;
    public $data ;

    public function __construct($data)
    {
        $this->id = GUID();
        $this->timeServed = new \DateTime('now');
        $this->servedBy=$_SERVER['SERVER_NAME'];
        $this->data = $data;

    }
    /*
     "id": "4e4cae03f48447e290edaa9f9bea2cf1",
    "apiVersion": null,
    "context": null,
    "servedBy": "RKO-P53S",
    "timeServed": "2022-10-12T11:37:14+00",
    "appVersion": "SB4 (RKO-P53S)",
    "took": 148.5615,
    "data":
     */
}