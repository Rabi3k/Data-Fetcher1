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
        $users = $this->GetUser($username,$password);
        if( count($users)>0)
        {
            $user = $users[0];
            if($user['user_name']===$username || $user['email']===$username )
            {
                $_SESSION["loggedin"] = true;
                $_SESSION["UserId"] = $user['id'];
                $_SESSION["username"] = $user['user_name'];
                $this->user= $user;
                return true;
            }
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
        $username = strtolower($username);
        /*$password = \mysql_escape_string($password);*/
        $statement = "SELECT * FROM $this->tblName WHERE (LOWER(user_name)=:username AND `password` = PASSWORD(:password))

        OR (LOWER(email)=:username AND `password` = PASSWORD(:password));";

        try {
            $sth = $this->db->prepare($statement);
            $sth->execute(array('password' => $password, 'username' => $username));
            $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

}