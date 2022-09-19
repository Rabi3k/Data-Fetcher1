<?php
/*
* @version  1.2.3
*author @Rabi3k
*/

require 'vendor/autoload.php';
require_once 'functions.php';
require_once 'dbScripts.php';

use Dotenv\Dotenv;

use Src\System\DatabaseConnector;
use Src\TableGateways\UserLoginGateway;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

require_once("config.php");



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
    $logger = new Logger('logger');
    $logger->pushHandler(new StreamHandler(__DIR__.'/logs/app.log', Monolog\Level::Debug));
    $logger->pushHandler(new FirePHPHandler());
    $_SESSION['logger'] = $logger;
    $logger->info('Logger is now Ready');
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