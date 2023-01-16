<?php

namespace Src\TableGateways;

use Src\Classes\User;
use Src\Classes\Profile;
use Src\Classes\Restaurant;
use Src\Classes\Branch;

use Pinq\Traversable;

class UserLoginGateway
{

    private $db = null;
    private $tblName = "`users`";
    private $user = null;
    private $loggedIn = null;

    public function GetUser()
    {
        return $this->user;
    }
    public function __construct($db)
    {
        $this->db = $db;
        $this->user = new User();
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
    function checkLogin()
    {
        if (isset($this->loggedIn)) {
            return $this->loggedIn;
        }
        if (!$_SESSION || !isset($_SESSION['loggedin']) || $_SESSION["loggedin"] === false) {
            $this->loggedIn = false;
        } else {
            $this->LoadUserClass($_SESSION["UserId"]);
            $this->loggedIn = true;
        }
        return $this->loggedIn;
    }
    function GetSecrets()
    {
        if (isset($this->user)) {
            return \json_encode($this->user->secrets);
        }
        return \json_encode(array());
    }

    function GetAllUsers()
    {
        $statement = "SELECT 

        u.`id`, 
        u.`email`, 
        u.`user_name`, 
        u.`full_name`, 
        u.`password`, 
        u.`secret_key`, 
        u.`profile_id`,  
        u.`IsAdmin`, 
        u.`isSuperAdmin`,
        u.`screen_type`,
        
        -- Profile
        p.`name` AS 'profile' ,
        p.`admin`  ,
        p.`super-admin`  
        
        FROM `users`as u
        LEFT JOIN `profiles` as p on (u.profile_id = p.id)";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $users = array();
            foreach ($result as $row) {
                $userSecrets = array();
                $profile = Profile::GetProfile(intval($row["profile_id"]), strval($row["profile"]), boolval($row["super-admin"]), boolval($row["admin"]));
                $user = User::GetUser(
                    intval($row["id"]),
                    strval($row["email"]),
                    strval($row["user_name"]),
                    strval($row["full_name"]),
                    strval($row["password"]),
                    strval($row["secret_key"]),
                    $profile,
                    array(),
                    array(),
                    boolval($row["IsAdmin"]),
                    boolval($row["isSuperAdmin"]),
                    intval($row["screen_type"])
                );
                array_push($users, $user);
            }
            return $users;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    function GetAllBranchesUsers($branchesId)
    {
        $brIds = implode(",",$branchesId);
        $statement = "SELECT 

        u.`id`, 
        u.`email`, 
        u.`user_name`, 
        u.`full_name`, 
        u.`password`, 
        u.`secret_key`, 
        u.`profile_id`,  
        u.`IsAdmin`, 
        u.`isSuperAdmin`,
        u.`screen_type`,
        
        -- Profile
        p.`name` AS 'profile' ,
        p.`admin`  ,
        p.`super-admin`  
        
        FROM `users`as u
        LEFT JOIN `profiles` as p on (u.profile_id = p.id)
        JOIN `user_relations` as ur on (u.id = ur.user_id )
        where ur.branch_id in ($brIds) and u.isSuperAdmin=0;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $users = array();
            foreach ($result as $row) {
                $userSecrets = array();
                $profile = Profile::GetProfile(intval($row["profile_id"]), strval($row["profile"]), boolval($row["super-admin"]), boolval($row["admin"]));
                $user = User::GetUser(
                    intval($row["id"]),
                    strval($row["email"]),
                    strval($row["user_name"]),
                    strval($row["full_name"]),
                    strval($row["password"]),
                    strval($row["secret_key"]),
                    $profile,
                    array(),
                    array(),
                    boolval($row["IsAdmin"]),
                    boolval($row["isSuperAdmin"]),
                    intval($row["screen_type"])
                );
                array_push($users, $user);
            }
            return $users;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    static function GetUserClass($id, bool $foceRest = true)
    {
        if (!isset($GLOBALS['dbConnection'])) {
            exit("db connection not loaded properly");
        }
        $ul = new UserLoginGateway($GLOBALS['dbConnection']);
        $ul->LoadUserClass($id, $foceRest);
        return $ul->GetUser();
    }

    function LoadUserClass($id, bool $foceRest = true)
    {
        $statement = "SELECT 

        u.`id`, 
        u.`email`, 
        u.`user_name`, 
        u.`full_name`, 
        u.`password`, 
        u.`secret_key`, 
        u.`profile_id`,  
        u.`IsAdmin`, 
        u.`isSuperAdmin`,
        u.`screen_type`,
        
        -- Profile
        p.`name` AS 'profile' ,
        p.`admin`  ,
        p.`super-admin`,
        
         -- restaurants
        CONCAT('[',GROUP_CONCAT(DISTINCT JSON_OBJECT(
		'id',r.`id`,
		'name',r.`name`,
		'phone',r.`phone`,
		'email',r.`email`,
		'cvr',r.`cvr`,
		'logo',r.`logo`,
		'reference_id',r.`reference_id`) SEPARATOR ','),
            ']') as 'restaurants',
            
        -- restaurant_branches
        CONCAT('[',
            GROUP_CONCAT(DISTINCT JSON_OBJECT(
                'id', rb.`id`,
                'restaurant_id', rb.`restaurant_id`,
                'reference_id', rb.`reference_id`,
                'city', rb.`city`,
                'zip_code', rb.`zip_code`,
                'address', rb.`address`,
                'country', rb.`country`,
                'cvr', rb.`cvr`)
            SEPARATOR ','),
            ']') AS 'branches',
            
	    -- restaurant_branch_keys
        CONCAT('[',
            GROUP_CONCAT(DISTINCT JSON_OBJECT(
                'branch_id', rbs.`branch_id`,
                'secret_key', rbs.`secret_key`)
            SEPARATOR ','),
            ']') AS 'secret_keys'

        FROM `users`as u
        LEFT JOIN `profiles` as p on (u.profile_id = p.id)
        LEFT JOIN `user_relations` as ur on (u.id = ur.user_id)
        LEFT JOIN `restaurant_branches` as rb1 on (ur.branch_id = rb1.id)
        LEFT JOIN `restaurants` as r on (IFNULL(ur.restaurant_id ,rb1.restaurant_id)= r.id ";
        $statement .= $foceRest ? "OR (IFNULL(ur.restaurant_id,0)=0 AND IFNULL(ur.branch_id,0)=0 AND u.isSuperAdmin = 1) )" : ") ";
        $statement .= "LEFT JOIN `restaurant_branches` as rb on (case when rb1.id is not null then rb1.id = rb.id else rb.restaurant_id = r.id end)
        LEFT JOIN `restaurant_branch_keys` as rbs on (rbs.branch_id = rb.id)
        
        WHERE u.`id` = ?
        
        GROUP BY u.`id`;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $userSecrets = array();
                $restaurants = array();
                $profile = Profile::GetProfile(intval($row["profile_id"]), strval($row["profile"]), boolval($row["super-admin"]), boolval($row["admin"]));

                $this->user = User::GetUser(
                    intval($row["id"]),
                    strval($row["email"]),
                    strval($row["user_name"]),
                    strval($row["full_name"]),
                    strval($row["password"]),
                    strval($row["secret_key"]),
                    $profile,
                    $userSecrets,
                    $restaurants,
                    boolval($row["IsAdmin"]),
                    boolval($row["isSuperAdmin"]),
                    intval($row["screen_type"]),
                );
                $rests = json_decode($row["restaurants"]);
                $rests = array_filter($rests, function ($r) {
                    return isset($r->id);
                });
                //var_dump($rests);
                if (isset($rests)) {
                    foreach ($rests as $r) {
                        $restaurant = Restaurant::Getrestaurant(
                            intval($r->id), //id
                            strval($r->email), //email
                            strval($r->name), //name
                            strval($r->phone), //phone
                            strval($r->cvr), //cvr
                            strval($r->logo), //logo
                            strval($r->reference_id), //reference_id
                            array() //branches
                        );
                        $branches = json_decode($row["branches"]);
                        $restBranch = array_filter($branches, function ($x) use ($r) {
                            return $x->restaurant_id === $r->id;
                        });
                        foreach ($restBranch as $br) {
                            $branch = Branch::GetBranch(
                                $br->id,
                                $br->restaurant_id,
                                $br->city,
                                $br->zip_code,
                                $br->address,
                                $br->country,
                                $br->cvr,
                                array(),
                                $br->reference_id
                            );
                            $secrets =  json_decode($row["secret_keys"]);
                            $brSecrets = array_filter($secrets, function ($x) use ($br) {
                                return $x->branch_id === $br->id;
                            });
                            foreach ($brSecrets as $s) {
                                array_push($branch->secrets, $s->secret_key);
                                array_push($userSecrets, $s->secret_key);
                            }

                            array_push($restaurant->branches, $branch);
                        }
                        array_push($restaurants, $restaurant);
                    }
                    $this->user->restaurants = $restaurants;
                    $this->user->secrets = $userSecrets;
                }

                
                //echo "<li><span> User => " . json_encode($this->user) . "<span></li>";

                break;
            }
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function updateUserRelations(array $userRelations)
    {
        //Password(:password), sha(:secret_key)
        $Dstatment = "DELETE FROM `user_relations`
WHERE `user_id` = :user_id;";

        $Istatement = "INSERT INTO `user_relations`
(`user_id`,
`restaurant_id`,
`branch_id`)
VALUES
";
        $secsStatment = array();
        foreach ($userRelations as $i) {
            $branch_id = $i->branch_id ?? 'null';
            $restaurant_id = $i->restaurant_id ?? 'null';
            $secStatment = "($i->user_id,$restaurant_id,$branch_id)";
            array_push($secsStatment, $secStatment);
        }
        $Istatement .= implode(",", $secsStatment) . ";";
        try {
            $statement = $this->db->prepare("$Dstatment");
            $this->db->beginTransaction();
            $statement->execute(array(
                'user_id' => $i->user_id
            ));
            $this->db->commit();
            $statement = $this->db->prepare("$Istatement");
            $this->db->beginTransaction();
            $statement->execute(array());
            $this->db->commit();

            return true;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    function GetEncryptedKey($userEmail)
    {
        /*AES_Encrypt(concat(`user_name`,'$',`secret_key`),'maxtibi1301') */

        $userEmail = strtolower($userEmail);
        /*$password = \mysql_escape_string($password);*/
        $statement = " SELECT AES_Encrypt(concat(`user_name`,'$',`secret_key`),'maxtibi1301') as 'SecretKey' from $this->tblName where email=:email ;";

        try {
            $sth = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $sth->execute(array('email' => $userEmail));
            $this->db->commit();
            $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
            $UserSecret = Traversable::from($result);
            return $UserSecret->first()['SecretKey'] ?? null;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    function DecryptSecretKey($secretKey)
    {
        /*AES_Encrypt(concat(`user_name`,'$',`secret_key`),'maxtibi1301') */

        /*$password = \mysql_escape_string($password);*/
        $statement = " SELECT AES_DECRYPT($secretKey,'maxtibi1301') as 'UserSecret';";

        try {
            $sth = $this->db->prepare($statement);

            $this->db->beginTransaction();
            $sth->execute();
            $this->db->commit();
            $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
            $UserSecret = Traversable::from($result);
            $str = $UserSecret
                ->select(function ($x) {
                    return explode('$', strval($x['UserSecret']));
                })->first();
            return $str;
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
            $sth = $this->db->prepare($statement);
            $sth->execute(array('secretKey' => $secretKey, 'username' => $username));
            $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
            $user = Traversable::from($result);
            if ($user->first() !== null) {
                $rval = $user->first();
                return User::GetUser(
                    intval($rval['id']),
                    $rval['email'],
                    $rval['user_name'],
                    $rval['full_name'],
                    $rval['password'],
                    $rval['secret_key'],
                    new Profile(),
                    array(),
                    array(),
                    boolval($rval['IsAdmin']),
                    boolval($rval['isSuperAdmin']),
                    intval($rval['screen_type']),
                );
            }
            return  null;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    function GetUserByUsernamePassword($username, $password)
    {
        $username = strtolower($username);
        /*$password = \mysql_escape_string($password);*/
        $statement = "SELECT * FROM $this->tblName WHERE (LOWER(user_name)=:username AND `password` = SHA2(:password,224))

        OR (LOWER(email)=:username AND `password` = SHA2(:password,224));";

        try {
            $sth = $this->db->prepare($statement);
            $sth->execute(array('password' => $password, 'username' => $username));
            $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    function InsertOrUpdate(User $input)
    {

        if ($input->id == 0) {
            return $this->InsertUser($input);
        } else {
            return $this->UpdateUser($input);
        }
    }
    private function InsertUser(User $input)
    {
        //Password(:password), sha(:secret_key)
        $statement = "INSERT INTO $this->tblName
        (`email`, `user_name`, `full_name`, `password`, `secret_key`, `profile_id`, `IsAdmin`, `isSuperAdmin`,`screen_type`)
         VALUES (:email, :user_name, :full_name, SHA2(:secret_key,224), SHA(:secret_key), :profile_id, :IsAdmin, :isSuperAdmin, :screen_type)";

        try {
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array(
                'email' => $input->email,
                'user_name' => $input->user_name,
                'full_name' => $input->full_name,
                'secret_key' => 'funneat',
                'profile_id' => $input->profile->id,
                'IsAdmin' => $input->isAdmin,
                'isSuperAdmin' => $input->isSuperAdmin,
                'screen_type' => $input->screen_type,
            ));
            $input->id = intval($this->db->lastInsertId());
            $this->db->commit();
            return $input;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    private function UpdateUser(User $input)
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
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array(
                'id' => (int)$input->id,
                'email' => $input->email,
                'user_name' => $input->user_name,
                'full_name' => $input->full_name,
                'profile_id' => $input->profile->id,
                'IsAdmin' => $input->isAdmin ?? false,
                'isSuperAdmin' => $input->isSuperAdmin ?? false,
                'screen_type' => $input->screen_type ?? 1,
            ));
            $this->db->commit();
            return $input;
        } catch (\PDOException $e) {
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
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array(
                'id' => (int)$this->user->id,
                'secret_key' => strval($password),
            ));
            $this->db->commit();
            return $this->user;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    function UpdateUserPassword(User $user, string $password)
    {
        //Password(:password), sha(:secret_key)
        $statement = "UPDATE $this->tblName
         SET 
         `password` =   SHA2(:secret_key,224) ,
         `secret_key`   =   SHA(:secret_key) 
         WHERE id   = :id;";

        try {
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array(
                'id' => (int)$user->id,
                'secret_key' => strval($password),
            ));
            $this->db->commit();
            return $user;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}
