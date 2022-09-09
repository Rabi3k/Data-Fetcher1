<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

use Src\System\DatabaseConnector;

use Src\TableGateways\UserLoginGateway;
if (session_status() === PHP_SESSION_NONE) {
    $dotenv = new DotEnv(__DIR__);
    $dotenv->load();

    $dbConnection = (new DatabaseConnector())->getConnection();

    $userLogin = new UserLoginGateway($dbConnection);

    $template = getenv('Template_Name');
    $templatePath = "templates/$template";

    $rootpath=getenv('ROOT_PATH');

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