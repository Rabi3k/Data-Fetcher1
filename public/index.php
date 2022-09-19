<?php
require "../bootstrap.php";

use Src\Controller\RequestController;
use Src\Controller\OrderController;
use Src\Controller\ActiveOrderController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$query = explode('/', strtolower($_SERVER['QUERY_STRING']));
$requestMethod = $_SERVER["REQUEST_METHOD"];
$oper = $_GET["q"];
$id = $_GET["id"] ?? null;
$startDate = $_GET["s"] ?? NULL;
$endDate = $_GET["e"] ?? null;
$secrets = $_GET["secrets"] ?? null;
$secrets = isset($secrets) ? json_decode($secrets) : array();
//echo file_get_contents('php://input');
//echo $_GET["secrets"]??"Get Nothing";
//echo $_POST["secrets"]??"Post nothing";
switch ($oper) {
    case 'request':
        // pass the request method and user ID to the PersonController and process the HTTP request:
        $controller = new RequestController($dbConnection, $requestMethod, $id);
        $controller->processRequest();
        break;
    case 'order':
        // pass the request method and user ID to the PersonController and process the HTTP request:
        $controller = new OrderController($dbConnection, $requestMethod, $id);
        $controller->processRequest();
        break;
    case 'Profile':
        break;
    case 'orders':
        $params = ([
            'id'        =>  $id,
            'startDate' =>  $startDate,
            'endDate'   =>  $endDate,
        ]);
        $controller = new ActiveOrderController($dbConnection, $requestMethod, $params, $secrets);
        $controller->processRequest();
        break;
    default:
        //$GLOBALS['http_response_code'] = 404;
        //http_response_code(404);
        //header('X-PHP-Response-Code: 404', true, 404);
        setResponseHead(404, 'Not Found');
        //header("HTTP/1.1 404 Not Found");
        //exit();
        //die("$oper Not Found");
        //exit();
}
function setResponseHead($httpStatusCode = 521, $httpStatusMsg  = 'Web server is down')
{
    $phpSapiName    = substr(php_sapi_name(), 0, 3);
    if ($phpSapiName == 'cgi' || $phpSapiName == 'fpm') {
        header('Status: ' . $httpStatusCode . ' ' . $httpStatusMsg);
    } else {
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
        header($protocol . ' ' . $httpStatusCode . ' ' . $httpStatusMsg);
    }
}

// all of our endpoints start with /person
// everything else results in a 404 Not Found
/*if ($uri[count($uri)-2] !== 'request') {
    header("HTTP/1.1 404 Not Found");
    exit();
}*/
// the user id is, of course, optional and must be a number:
