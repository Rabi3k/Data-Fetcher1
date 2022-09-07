<?php
namespace Src\TableGateways;

class UserLoginGateway {
    function ValidateLogin($username)
    {
    $_SESSION["loggedin"] = true;
    //$_SESSION["id"] = $id;
    $_SESSION["username"] = $username;
       // header("Location: /kds");
    }
    function checkLogin()
    {
        if (!$_SESSION || !isset($_SESSION['loggedin']) || $_SESSION["loggedin"] === false ) 
        {          
            return false;
        }
        return true;
    }
}