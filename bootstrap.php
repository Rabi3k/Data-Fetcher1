<?php
/*
* @version  1.2.3
*author @Rabi3k
*/

require_once 'vendor/autoload.php';
require_once 'functions.php';
require_once 'dbScripts.php';

use Dotenv\Dotenv;

use Monolog\Logger;
use Src\Classes\Loggy;

use Src\System\DatabaseConnector;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Src\Classes\Options;
use Src\TableGateways\OptionsGateway;
use Src\TableGateways\UserGateway;
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
        'VERSION'
    ]);
    } catch(Exception $e)
    {
            exit("Site Error please contact !");
        }


    $template = getenv('Template_Name');
    $rootpath=getenv('ROOT_PATH');
    $dbConnection = (new DatabaseConnector())->getConnection();
    
    $smtpA = (new OptionsGateway($dbConnection))->findByType('SMTP');
    $smtp=Options::classToArray($smtpA);

    $GithubA = (new OptionsGateway($dbConnection))->findByType('Github');
    $Github=Options::classToArray($GithubA);
    $githubToken = base64_decode("Z2hwX0V3cFp4V3VQTk1QcFE4anFlNXUzakJDSWo2TUV3SjF0N2NMYg==");
    define('GIT_ACCESS_TOKEN',$githubToken);
    
    $userGateway = new UserGateway($dbConnection);
    
    $templatePath = "templates/$template";
    ini_set('session.referer_check', 'TRUE');
    session_start();
    
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


if(!isset($_SESSION['Loggy']))
{
    
    $lo = new Loggy();
    /*$logger = new Logger('logger');
    $logger->pushHandler(new StreamHandler(__DIR__.'/logs/app.log', Monolog\Level::Debug));
    $logger->pushHandler(new FirePHPHandler());
    $_SESSION['logger'] = $logger;
    $logger->info('Logger is now Ready');*/
    //echo 'Logger is now Ready';
}
else
{
    $lo =   $_SESSION['Loggy'];
    //$logger->info('Logger is loaded');
}

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