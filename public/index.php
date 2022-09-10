<?php
require "../bootstrap.php";
use Src\Controller\RequestController;
use Src\Controller\OrderController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$query = explode( '/',strtolower($_SERVER['QUERY_STRING']));
$requestMethod = $_SERVER["REQUEST_METHOD"];
switch($query[0])
{
    case 'request':
        $reqId = null;
        if (isset($query[1])) {
            $reqId = (int) $query[1];
        }


        // pass the request method and user ID to the PersonController and process the HTTP request:
        $controller = new RequestController($dbConnection, $requestMethod, $reqId);
        $controller->processRequest();
        break;
    case 'order':
        $orderId = null;
        if (isset($query[1])) {
            $orderId = (int) $query[1];
        }

        // pass the request method and user ID to the PersonController and process the HTTP request:
        $controller = new OrderController($dbConnection, $requestMethod, $orderId);
        $controller->processRequest();
        break;
    case 'orders':
        $controller = new OrderController($dbConnection, $requestMethod, null);
        $controller->getActiveOrderIds();
            break;
    default:
        header("HTTP/1.1 404 Not Found");
        //exit();
        die("Not Found");
}


// all of our endpoints start with /person
// everything else results in a 404 Not Found
/*if ($uri[count($uri)-2] !== 'request') {
    header("HTTP/1.1 404 Not Found");
    exit();
}*/
// the user id is, of course, optional and must be a number:
