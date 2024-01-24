<?php 
namespace Src\Classes;

class GlobalFunctions{

    public static function setResponseHead($httpStatusCode = 521,$httpStatusMsg  = 'Web server is down')
    {
        $phpSapiName    = substr(php_sapi_name(), 0, 3);
        if ($phpSapiName == 'cgi' || $phpSapiName == 'fpm') {
            header('Status: '.$httpStatusCode.' '.$httpStatusMsg );
        } else {
            $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
            header($protocol.' '.$httpStatusCode.' '.$httpStatusMsg);
        }
    }
    public static function ClassObjArrToJsonStr(array $classObjArr)
    {
        $strs = array();
        foreach ($classObjArr as $key => $value) {
            $strs[]=$value->getJsonStr();
            # code...
        }
        $str = implode(",",$strs);
        return "[$str]";
    }
}