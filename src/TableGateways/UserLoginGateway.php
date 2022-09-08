<?php
namespace Src\TableGateways;

class UserLoginGateway {

    private $db = null;
    private $tblName = "`users`";
    private $user=null;

    public function __construct($db)
    {
        $this->db = $db;
    }
    function ValidateLogin($username,$password)
    {
        $user = $this->GetUser($username,$password);
        if($user && isset($user[0]) && $user[0]['user_name']===$username)
        {
            echo 'Passed';
            $_SESSION["loggedin"] = true;
            //$_SESSION["id"] = $id;
            $_SESSION["username"] = $username;
               // header("Location: /kds");
            $this->user= $user;
            return true;
        }
        return false;
   
    }
    function checkLogin()
    {
        if (!$_SESSION || !isset($_SESSION['loggedin']) || $_SESSION["loggedin"] === false ) 
        {          
            return false;
        }
        return true;
    }
    function GetUser($username,$password)
    {
       /* $username = \mysql_escape_string($username);
        $password = \mysql_escape_string($password);*/
        $statement = "SELECT * FROM $this->tblName WHERE user_name='$username' AND `password` = PASSWORD('$password');";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

}