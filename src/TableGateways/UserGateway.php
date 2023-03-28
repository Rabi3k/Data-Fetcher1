<?php

namespace Src\TableGateways;

use Pinq\Traversable;
use Src\Classes\Loggy;
use Src\Classes\LoginUser;

use Src\System\DbObject;

class UserGateway extends DbObject
{

    static LoginUser $user;

    #region protected functions
    protected function SetSelectStatment()
    {
        $tblname = $this->getTableName();
        $this->selectStatment = "SELECT * FROM $tblname;";
    }
    protected function SetTableName()
    {
        $this->tblName = "tbl_users";
    }
    #endregion
    #region public functions
    public function FindById($id): LoginUser|null
    {
        $statment = 'SELECT 

            /* User */
            JSON_OBJECT(
                "id", u.`id`,
                "email", u.`email`,
                "user_name", u.`user_name`,
                "full_name", u.`full_name`,
                "password", u.`password`,
                "secret_key", u.`secret_key`,
                "profile_id", u.`profile_id`,
                "IsAdmin", u.`IsAdmin`,
                "isSuperAdmin", u.`isSuperAdmin`,
                "screen_type", u.`screen_type`,
                "Restaurants_Id", concat("[",group_concat(distinct r.`gf_refid`),"]"),
                "Profile",CONVERT(GROUP_CONCAT(DISTINCT case when p.id is null then "" else JSON_OBJECT(
                "id", p.`id`,
                "admin",ifnull( p.`admin`,0),
                "super-admin", ifnull( p.`super-admin`,0)
             ) end SEPARATOR \',\'),  JSON),
              "companies",CONVERT(CONCAT (\'[\',GROUP_CONCAT(DISTINCT case when c.id is null then "" else JSON_OBJECT(
                "id", c.`id`,
                "name", c.`name`,
                "cvr_nr", c.`cvr_nr`,
                "address", c.`address`,
                "city", c.`city`,
                "zip", c.`zip`,
                "email", c.`email`,
                "phone", c.`phone`,
                "gf_refid", c.`gf_refid`
             ) end SEPARATOR \',\'),
                    \']\'),  JSON),
              "restaurants",CONVERT(CONCAT (\'[\',GROUP_CONCAT(DISTINCT case when r.id is null then "" else JSON_OBJECT(
                "id", r.`id`,
                "company_id", r.`company_id`,
                "name", r.`name`,
                "alias", r.`alias`,
                "p_nr", r.`p_nr`,
                "address", r.`address`,
                "city", r.`city`,
                "post_nr", r.`post_nr`,
                "country", r.`country`,
                "email", r.`email`,
                "phone", r.`phone`,
                "is_gf", r.`is_gf`,
                "is_managed", r.`is_managed`,
                "gf_refid", r.`gf_refid`,
                "gf_urid", r.`gf_urid`,
                "gf_cdn_base_path", r.`gf_cdn_base_path`
             ) end  SEPARATOR \',\'),
                    \']\'),  JSON)
             
             )as "user"
            FROM `tbl_users` u
            LEFT JOIN `tbl_profiles` as p on (u.profile_id = p.id)
            LEFT JOIN `tbl_user_relations` ur on(u.id = ur.user_id)
            LEFT JOIN `tbl_restaurants` as r1 on (ur.restaurant_id = r1.id)
            LEFT JOIN `tbl_companies` as c on (IFNULL(ur.company_id ,r1.company_id)= c.id OR (IFNULL(ur.company_id,0)=0 AND IFNULL(ur.restaurant_id,0)=0 AND u.isSuperAdmin = 1) )
            LEFT JOIN `tbl_restaurants` as r on (case when r1.id is not null then r1.id = r.id else r.company_id = c.id end)
            where u.`id` = ' . $id . '
            group by u.id;';
        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            return LoginUser::GetUserFromJsonStr($result[0]["user"]);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    static function GetUserClass($id, bool $foceRest = true)
    {
        if (!isset($GLOBALS['dbConnection'])) {
            exit("db connection not loaded properly");
        }
        $ul = new UserGateway($GLOBALS['dbConnection']);
        $u = $ul->FindById($id);
        return $u->Getjson();
    }
    function GetUserByUsernamePassword($username, $password)
    {
        $username = strtolower($username);
        /*$password = \mysql_escape_string($password);*/
        $statement = "SELECT * FROM $this->tblName WHERE (LOWER(user_name)=:username AND `password` = SHA2(:password,224))

        OR (LOWER(email)=:username AND `password` = SHA2(:password,224));";

        try {
            $sth = $this->getDbConnection()->prepare($statement);
            $sth->execute(array('password' => $password, 'username' => $username));
            $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    function GetUserByUsernameSecretKey($username, $secretKey)
    {

        $username = strtolower($username);
        /*$password = \mysql_escape_string($password);*/
        $statement = "SELECT * FROM $this->tblName 
        WHERE (LOWER(user_name)=:username AND `secret_key` = :secretKey)
        OR (LOWER(email)=:username AND `secret_key` = :secretKey);";

        try {
            $sth = $this->getDbConnection()->prepare($statement);
            $sth->execute(array('secretKey' => $secretKey, 'username' => $username));
            $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    function ValidateLogin($username, $password)
    {
        $users = $this->GetUserByUsernamePassword($username, $password);
        if (count($users) > 0) {
            $user = $users[0];

            if (strtolower($user['user_name']) === strtolower($username) || strtolower($user['email']) === strtolower($username)) {
                $_SESSION["loggedin"] = true;
                $_SESSION["UserId"] = $user['id'];
                $_SESSION["username"] = $user['user_name'];
                //$this->LoadUserClass($user['id']);
                return true;
            }
        }

        return false;
    }
    function ValidateLoginBySecretKey($username, $secretKey)
    {

        $user = $this->GetUserByUsernameSecretKey($username, $secretKey);

        if ($user) {
            if (strtolower($user->user_name) === strtolower($username) || strtolower($user->email) === strtolower($username)) {
                $_SESSION["loggedin"] = true;
                $_SESSION["UserId"] = $user->id;
                $_SESSION["username"] = $user->user_name;
                //$this->LoadUserClass($user['id']);
                return true;
            }
        }

        return false;
    }
    public function GetUser()
    {
        return $this::$user;
    }
    function checkLogin()
    {
        $loggedIn = false;

        if (!$_SESSION || !isset($_SESSION['loggedin']) || $_SESSION["loggedin"] === false) {
            $this::$user = LoginUser::NewUser();
            $loggedIn = false;
        } else {
            if (isset($this::$user) && $this::$user->id > 0) {
                $loggedIn = true;
            } else {
                $this::$user = $this->FindById($_SESSION["UserId"]);
                if (isset($this::$user) && $this::$user->id != null) {
                    $loggedIn = true;
                }
            }
        }
        return $loggedIn;
    }
    #endregion
}
