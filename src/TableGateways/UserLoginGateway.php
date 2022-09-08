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
        $user = $this->GetUser($username,$password)[0];
        if($user && isset($user) && ($user['user_name']===$username || $user['email']===$username ))
        {
           // echo 'Passed';
            $_SESSION["loggedin"] = true;
            $_SESSION["UserId"] = $id;
            $_SESSION["username"] = $user['user_name'];
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
        $statement = "SELECT * FROM $this->tblName WHERE (LOWER(user_name)=LOWER(:username) AND `password` = PASSWORD(:password))

        OR (LOWER(email)=LOWER(:username) AND `password` = PASSWORD(:password));";

        try {
            $sth = $this->db->prepare($statement);
            $sth->execute(array('password' => $password, 'username' => $username));
            $result = $sth->fetchAll();
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

}