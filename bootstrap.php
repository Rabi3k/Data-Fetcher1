<?php
/*
* @version  1.2.3
*author @Rabi3k
*/

require 'vendor/autoload.php';
require_once 'functions.php';
require_once 'dbScripts.php';

use Dotenv\Dotenv;

use Monolog\Logger;
use Src\Classes\Loggy;

use Src\System\DatabaseConnector;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Src\TableGateways\UserLoginGateway;

require_once("config.php");
//$config = yaml_parse_file("config.yaml");


if (session_status() === PHP_SESSION_NONE) {

    $dotenv = new DotEnv(__DIR__);
    $dotenv->load();
    try{
    $dotenv->required([
        'DB_HOST',
        'DB_PORT',
        'DB_DATABASE',
        'DB_USERNAME',
        'DB_PASSWORD',
        'Template_Name',
        'ROOT_PATH',
        'SMTP_Host',
        'SMTP_Host',
        'SMTP_SMTPAuth',
        'SMTP_Username',
        'SMTP_Password',
        'SMTP_Port',
        'SMTP_SMTPSecure',
        'VERSION'
    ]);
    } catch(Exception $e)
    {
            exit("Site Error please contact !");
        }
    $template = getenv('Template_Name');
    $rootpath=getenv('ROOT_PATH');
    $dbConnection = (new DatabaseConnector())->getConnection();

    $userLogin = new UserLoginGateway($dbConnection);
    
    $templatePath = "templates/$template";
    ini_set('session.referer_check', 'TRUE');
    session_start();
    
}

if(!isset($_SESSION['logger']))
{
    

    /*$logger = new Logger('logger');
    $logger->pushHandler(new StreamHandler(__DIR__.'/logs/app.log', Monolog\Level::Debug));
    $logger->pushHandler(new FirePHPHandler());
    $_SESSION['logger'] = $logger;
    $logger->info('Logger is now Ready');*/
    //echo 'Logger is now Ready';
}
else
{
    $logger =   $_SESSION['logger'];
    //$logger->info('Logger is loaded');
}

if(!isset($rootpath))
{
    $rootpath=$_SESSION['ROOT_PATH'];
}
else
{
    $_SESSION['ROOT_PATH'] = $rootpath;
}


global $lo;
$lo = new Loggy();

function exception_handler(Throwable $exception) {
    global $lo;
    $lo->logy($exception->getMessage(),$exception->getTraceAsString(),$exception);
  }
  
  set_exception_handler('exception_handler');


/*
register_shutdown_function(function(){
    $error = error_get_last();
    if($error){
        global $lo;
        $lo->logy($error['message'],"",$error);
    }
});*/

set_error_handler(
    function($level, $error, $file, $line){
        if(0 === error_reporting()){
            return false;
        }
        global $lo;
        $lo->logy("","",array('level'=>$level,'error'=> $error,'file'=> $file,'line'=> $line));
    },
    E_ALL
);