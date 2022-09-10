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
$oper = $_GET["q"];
$id = $_GET["id"]??null;
$startDate = $_GET["s"]??NULL;
$endDate = $_GET["e"]??null;
$secrets = $_GET["secrets"]??null;
//echo file_get_contents('php://input');
//echo $_GET["secrets"]??"Get Nothing";
//echo $_POST["secrets"]??"Post nothing";
switch($oper)
{
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
    case 'orders':
        $controller = new OrderController($dbConnection, $requestMethod, null);

        if(isset($startDate) && isset($endDate) && $requestMethod==='GET')
        {
            $secrets = json_decode($secrets);
            if(!isset($secrets) || count($secrets)<1)
            {
                header('HTTP/1.1 401 Unauthorized');
                exit();    
            }
           // var_dump($body->secrets);
            $response =  $controller->getActiveOrderIdsByDate($startDate,$endDate,$secrets);    
            header($response['status_code_header']);
            if ($response['body']) 
            {
                echo $response['body'];
            }
        }
        $controller->getActiveOrderIds();
            break;
    default:
        header("HTTP/1.1 404 Not Found");
        //exit();
        die("$oper Not Found");
}


// all of our endpoints start with /person
// everything else results in a 404 Not Found
/*if ($uri[count($uri)-2] !== 'request') {
    header("HTTP/1.1 404 Not Found");
    exit();
}*/
// the user id is, of course, optional and must be a number:
