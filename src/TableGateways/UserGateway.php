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
    public function GetAllUsers()
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
                "Restaurants_Id", "[]",
                "Profile",JSON_ARRAYAGG( 
         DISTINCT 
         JSON_OBJECT( 
            "id", p.`id`, 
            "name",p.`name`, 
            "admin",ifnull( p.`admin`,0), 
            "super-admin", ifnull( p.`super-admin`,0) 
            ) 
            ),
              "companies","[]",
              "restaurants","[]"
             )as "user"
            FROM `tbl_users` u
            LEFT JOIN `tbl_profiles` as p on (u.profile_id = p.id)
            LEFT JOIN `tbl_user_relations` ur on(u.id = ur.user_id)
            GROUP BY u.id;';
        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $this->getDbConnection()->query('SET SESSION group_concat_max_len=150000');
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();

            if (count($result) < 1) {
                return null;
            }
            return LoginUser::GetUserFromArrayJsonStr($result);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function FindById($id, $forceLoad = true): LoginUser|null
    {
        $statment = 'SELECT 
        /* User */ 
        JSON_OBJECT( 
        "id", u.`id`, 
        "email", u.`email`, 
        "user_name", u.`user_name`, "full_name", u.`full_name`, 
        "password", u.`password`, 
        "secret_key", u.`secret_key`, 
        "profile_id", u.`profile_id`, 
        "IsAdmin", u.`IsAdmin`, 
        "isSuperAdmin", u.`isSuperAdmin`,
         "screen_type", u.`screen_type`, 
         "passkey",u.`passkey`,
		 "funneat_user",u.`funneat_user`,
		 "funneat_pass",u.`funneat_pass`,
         "Restaurants_Id", Json_Array(group_concat(distinct r.`gf_refid`)), 
         "Profile",JSON_ARRAYAGG( 
         DISTINCT 
         JSON_OBJECT( 
            "id", p.`id`, 
            "name",p.`name`, 
            "admin",ifnull( p.`admin`,0), 
            "super-admin", ifnull( p.`super-admin`,0) 
            ) 
            ),
            "companies", case when c.`id` is not null then JSON_ARRAYAGG(DISTINCT 
         JSON_OBJECT
         ( "id", c.`id`, 
            "name", c.`name`, 
            "cvr_nr", c.`cvr_nr`, 
            "address", c.`address`, 
            "city", c.`city`, 
            "zip", c.`zip`, 
            "email", c.`email`, 
            "phone", c.`phone`, 
            "gf_refid", c.`gf_refid`
            )  
            )else JSON_ARRAY() End,
         "restaurants",
         case when r.`id` is not null then
         JSON_ARRAYAGG(DISTINCT 
         JSON_OBJECT( "id", r.`id`, 
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
         "is_gf",  convert(r.`is_gf`,int), 
         "is_managed", convert(r.`is_managed`,int), 
         "gf_refid", r.`gf_refid`,
         "gf_urid", convert(r.`gf_urid`,varchar(64))) )else JSON_ARRAY() End        
         )as "user"
        
            FROM `tbl_users` u
            LEFT JOIN `tbl_profiles` as p on (u.profile_id = p.id)
            LEFT JOIN `tbl_user_relations` ur on(u.id = ur.user_id)
            LEFT JOIN `tbl_restaurants` as r1 on (ur.restaurant_id = r1.id)
            LEFT JOIN `tbl_companies` as c on (IFNULL(ur.company_id ,r1.company_id)= c.id';
        //OR (IFNULL(ur.company_id,0)=0 AND IFNULL(ur.restaurant_id,0)=0 AND u.isSuperAdmin = 1) )
        $statment .= $forceLoad ? ' OR (IFNULL(ur.company_id,0)=0 AND IFNULL(ur.restaurant_id,0)=0 AND u.isSuperAdmin = 1))' : ')';
        $statment .= 'LEFT JOIN `tbl_restaurants` as r on (case when r1.id is not null then r1.id = r.id else r.company_id = c.id end)
            where u.`id` = ' . $id . '
            group by u.id;';
        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $this->getDbConnection()->query('SET SESSION group_concat_max_len=150000');
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();

            if (count($result) < 1) {
                return null;
            }
            $user = LoginUser::GetUserFromJsonStr($result[0]["user"]);
            $user->passkey = isset($user->passkey) ? str_Encrypt($user->passkey) : "";
            //$user->passkey = isset($user->passkey) ? str_Decrypt($user->passkey) : "";
            return $user;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    static function GetUserClass($id, bool $foceRest = true): LoginUser|null
    {
        if (!isset($GLOBALS['dbConnection'])) {
            exit("db connection not loaded properly");
        }
        $ul = new UserGateway($GLOBALS['dbConnection']);
        $u = $ul->FindById($id, $foceRest);
        return $u;
    }
    function GetUserByUsernamePassword($username, $password)
    {
        $username = strtolower($username);
        /*$password = \mysql_escape_string($password);*/
        $statement = "SELECT * FROM $this->tblName WHERE (LOWER(user_name)=LOWER(:username) OR LOWER(email)=LOWER(:username)) AND `password` = SHA2(:password,224);";

        try {
            $sth = $this->getDbConnection()->prepare($statement);
            $sth->execute(array('password' => $password, 'username' => $username));
            $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
            if (count($result) > 0) {
                return (object)$result[0];
            } else {
                return null;
            }
            echo json_encode((object)$result[0]);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    function GetUserByPasskey($passkey): LoginUser|null
    {
            $uri = str_Decrypt($passkey);

        $pattern = '{\w{2}(?<id>\w{4})\w{4}}';
        if (preg_match($pattern, $uri, $matches)) {
            //echo ("User id: " . hexdec($matches['id']));
            $userId = hexdec($matches['id']);
            /*$password = \mysql_escape_string($password);*/
            $statement = "SELECT id FROM $this->tblName WHERE id=:userId AND  passkey=:passkey;";
            //echo "$statement => $uri";
            try {
                $sth = $this->getDbConnection()->prepare($statement);
                $sth->execute(array('userId' => $userId, 'passkey' => $uri));
                $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    return $this->FindById($result[0]["id"]);
                } else {
                    return (new LoginUser());
                }
            } catch (\PDOException $e) {
                (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
                exit($e->getMessage());
            }
        }
        return new LoginUser();
    }
    function GetEncryptedKey($userEmail)
    {
        /*AES_Encrypt(concat(`user_name`,'$',`secret_key`),'maxtibi1301') */

        $userEmail = strtolower($userEmail);
        /*$password = \mysql_escape_string($password);*/
        $statement = " SELECT AES_Encrypt(concat(`user_name`,'$',`secret_key`),'maxtibi1301') as 'SecretKey' from $this->tblName where email=:email ;";

        try {
            $sth = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $sth->execute(array('email' => $userEmail));
            $this->getDbConnection()->commit();
            $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
            $UserSecret = Traversable::from($result);
            return $UserSecret->first()['SecretKey'] ?? null;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    function DecryptSecretKey($secretKey)
    {
        /*AES_Encrypt(concat(`user_name`,'$',`secret_key`),'maxtibi1301') */

        /*$password = \mysql_escape_string($password);*/
        $statement = " SELECT AES_DECRYPT($secretKey,'maxtibi1301') as 'UserSecret';";

        try {
            $sth = $this->getDbConnection()->prepare($statement);

            $this->getDbConnection()->beginTransaction();
            $sth->execute();
            $this->getDbConnection()->commit();
            $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
            $UserSecret = Traversable::from($result);
            $str = $UserSecret
                ->select(function ($x) {
                    return explode('$', strval($x['UserSecret']));
                })->first();
            return $str;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
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
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    function ValidateLogin($username, $password)
    {
        $users = $this->GetUserByUsernamePassword($username, $password);
        if (isset($users)) {
            $user = $users;

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
    function ValidateLoginBySecretKey($username, $secretKey)
    {

        $user = LoginUser::GetUser($this->GetUserByUsernameSecretKey($username, $secretKey)[0]);

        if ($user) {
            echo $user->id;
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
            } else if (isset($_SESSION["User"]) && $_SESSION["User"]->id > 0) {
                $this::$user = $_SESSION["User"];
                $loggedIn = true;
            } else {
                $us = $this->FindById($_SESSION["UserId"]);
                $_SESSION["User"] = $us;
                $this::$user = $us;
                if (isset($this::$user) && $this::$user->id != null) {
                    $loggedIn = true;
                }
            }
        }

        return $loggedIn;
    }

    function InsertOrUpdate(LoginUser $input)
    {

        if ($input->id == 0) {
            return $this->InsertUser($input);
        } else {
            return $this->UpdateUser($input);
        }
    }
    private function InsertUser(LoginUser $input)
    {
        //Password(:password), sha(:secret_key)
        $statement = "INSERT INTO $this->tblName
        (`email`, `user_name`, `full_name`, `password`, `secret_key`, `profile_id`, `IsAdmin`, `isSuperAdmin`,`screen_type`)
         VALUES (:email, :user_name, :full_name, SHA2(:secret_key,224), SHA(:secret_key), :profile_id, :IsAdmin, :isSuperAdmin, :screen_type)";

        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'email' => $input->email,
                'user_name' => $input->user_name,
                'full_name' => $input->full_name,
                'secret_key' => 'funneat',
                'profile_id' => $input->profile_id,
                'IsAdmin' => $input->isAdmin,
                'isSuperAdmin' => $input->isSuperAdmin,
                'screen_type' => $input->screen_type,
            ));
            $input->id = intval($this->getDbConnection()->lastInsertId());
            $this->getDbConnection()->commit();
            return $input;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    private function UpdateUser(LoginUser $input)
    {
        //Password(:password), sha(:secret_key)
        $statement = "UPDATE $this->tblName
         SET 
         `email` =   :email ,
         `user_name` =   :user_name ,
         `full_name` =   :full_name ,
         `profile_id` =   :profile_id ,
         `IsAdmin` =   :IsAdmin ,
         `isSuperAdmin` =   :isSuperAdmin,
         `screen_type` =   :screen_type 

         WHERE id   = :id;";

        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'id' => (int)$input->id,
                'email' => $input->email,
                'user_name' => $input->user_name,
                'full_name' => $input->full_name,
                'profile_id' => $input->profile_id,
                'IsAdmin' => $input->isAdmin ?? false,
                'isSuperAdmin' => $input->isSuperAdmin ?? false,
                'screen_type' => $input->screen_type ?? 1,
            ));
            $this->getDbConnection()->commit();
            return $input;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function UpdateUserEmail(LoginUser $input)
    {
        //Password(:password), sha(:secret_key)
        $statement = "UPDATE $this->tblName
         SET 
		 `funneat_user` = :funneat_user,
		 `funneat_pass` = :funneat_pass

         WHERE `id`   = :id;";

        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'id' => (int)$input->id,
                'funneat_user' => $input->funneat_user,
                'funneat_pass' => $input->funneat_pass,
            ));
            $this->getDbConnection()->commit();
            return $input;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    public function updateUserRelations($userRelations)
    {
        /*
        $userRelations 
        {
            'userId':<int>,
            'relations': <Array>
                [{
                    'companyId': <int>,
                    'restaurantId': <Array (int) CAN BE NULL !>
                }]
        }
        */
        //Password(:password), sha(:secret_key)
        $Dstatment = "DELETE FROM `tbl_user_relations`
WHERE `user_id` = :user_id;";

        $secsStatment = array();
        if (count($userRelations->relations) < 1) {
            $secStatment = "(:user_id,NULL,NULL)";
            array_push($secsStatment, $secStatment);
        }
        foreach ($userRelations->relations as $r) {
            $company_id = $r->companyId ?? 'null';
            if ($r->restaurantId == null || count($r->restaurantId) < 1) {
                $secStatment = "(:user_id,$company_id,NULL)";
                array_push($secsStatment, $secStatment);
            } else {
                foreach ($r->restaurantId as $key => $restId) {
                    $secStatment = "(:user_id,$company_id,$restId)";
                    array_push($secsStatment, $secStatment);
                }
            }
        }
        /**
         * 
         * 
         */
        $Istatement = "INSERT INTO `tbl_user_relations`
        (`user_id`,
        `company_id`,
        `restaurant_id`)
        VALUES
        " . implode(",", $secsStatment) . ";";
        try {
            $statement = $this->getDbConnection()->prepare("$Dstatment");
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'user_id' => $userRelations->userId
            ));
            $this->getDbConnection()->commit();
            $statement = $this->getDbConnection()->prepare("$Istatement");
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'user_id' => $userRelations->userId
            ));
            $this->getDbConnection()->commit();

            return true;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    function UpdatePassword(string $password)
    {
        //Password(:password), sha(:secret_key)
        $statement = "UPDATE $this->tblName
         SET 
         `password` =   SHA2(:secret_key,224) ,
         `secret_key`   =   SHA(:secret_key) 
         WHERE id   = :id;";

        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'id' => (int)$this->user->id,
                'secret_key' => strval($password),
            ));
            $this->getDbConnection()->commit();
            return $this->user;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    function UpdateUserPassword(LoginUser $user, string $password)
    {
        //Password(:password), sha(:secret_key)
        $statement = "UPDATE $this->tblName
         SET 
         `password` =   SHA2(:secret_key,224) ,
         `secret_key`   =   SHA(:secret_key) 
         WHERE id   = :id;";

        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'id' => (int)$user->id,
                'secret_key' => strval($password),
            ));
            $this->getDbConnection()->commit();
            return $user;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    function UpdateUserPasskey(LoginUser $user, string $passkey)
    {
        //Password(:password), sha(:secret_key)
        $statement = "UPDATE $this->tblName
         SET 
         `passkey` =  passkey
         WHERE id   = :id;";

        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'id' => (int)$user->id,
                'passkey' => strval($passkey),
            ));
            $this->getDbConnection()->commit();
            return $user;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    #endregion
}
