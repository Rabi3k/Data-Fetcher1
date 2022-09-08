<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

use Src\System\DatabaseConnector;

use Src\TableGateways\UserLoginGateway;

$dotenv = new DotEnv(__DIR__);
$dotenv->load();

$dbConnection = (new DatabaseConnector())->getConnection();

$userLogin = new UserLoginGateway();

$template = getenv('Template_Name');
$templatePath = "templates/$template";

$rootpath=getenv('Rootpath');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.referer_check', 'TRUE');
    session_start();
}

$_SESSION['Root_Path'] = $rootpath;