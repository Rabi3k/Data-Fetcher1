<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

use Src\System\DatabaseConnector;
use Src\TableGateways\UserLoginGateway;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

if (session_status() === PHP_SESSION_NONE) {
    $dotenv = new DotEnv(__DIR__);
    $dotenv->load();

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