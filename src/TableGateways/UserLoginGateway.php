<?php
namespace Src\TableGateways;
use Src\Classes\User;
use Src\Classes\Profile;
use Src\Classes\Restaurant;
use Src\Classes\Branch;


class UserLoginGateway {

    private $db = null;
    private $tblName = "`users`";
    private $user=null;
    private $loggedIn =null;

    public function GetUser()
    {
        return $this->user;
    }
    public function __construct($db)
    {
        $this->db = $db;
        $this->user=new User();
    }
    function ValidateLogin($username,$password)
    {
        $users = $this->GetUserByUsernamePassword($username,$password);
        if( count($users)>0)
        {
            $user = $users[0];

            if(strtolower($user['user_name'])===strtolower($username) || strtolower($user['email'])===strtolower($username ))
            {
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
        if(isset($this->loggedIn))
        {
            return $this->loggedIn;
        }
        if (!$_SESSION || !isset($_SESSION['loggedin']) || $_SESSION["loggedin"] === false ) 
        {        
            $this->loggedIn = false;  
        }
        else
        {
            $this->LoadUserClass($_SESSION["UserId"]);
            $this->loggedIn = true;
        }
        return $this->loggedIn;
    }
    function GetSecrets()
    {
        if(isset($this->user))
        {
           return \json_encode($this->user->secrets);
        }
        return \json_encode(array());
    }
    function LoadUserClass($id)
    {
        $statement = "SELECT 

        u.`id`, 
        u.`email`, 
        u.`user_name`, 
        u.`full_name`, 
        u.`password`, 
        u.`secret_key`, 
        u.`profile_id`, 
        
        -- Profile
        p.`Name` AS 'profile' ,
        
        -- restaurants
        GROUP_CONCAT(DISTINCT Concat(r.`id`,',',r.`name`,',',r.`phone`,',',r.`email`,',',r.`cvr`,',',r.`logo`, ',',r.`reference_id`) SEPARATOR '$') as 'restaurants',
        
        -- restaurant_branches
        GROUP_CONCAT(DISTINCT Concat(rb.`id`,',',rb.`restaurant_id`,',',rb.`city`,',',rb.`zip_code`,',',rb.`address`,',',rb.`country`,',',IFNULL(rb.`cvr`, '0')) SEPARATOR '$') as 'branches',

        -- restaurant_branch_keys
        GROUP_CONCAT(DISTINCT Concat( rbs.`branch_id`,',',rbs.`secret_key`) SEPARATOR '$') as 'secret_keys' 

        FROM `users`as u
        LEFT JOIN `profiles` as p on (u.profile_id = p.id)
        LEFT JOIN `user_relations` as ur on (u.id = ur.user_id)
        LEFT JOIN `restaurant_branches` as rb1 on (ur.branch_id = rb1.id)
        LEFT JOIN `restaurants` as r on (IFNULL(ur.restaurant_id ,rb1.restaurant_id)= r.id OR (IFNULL(ur.restaurant_id,1)=1 AND IFNULL(ur.branch_id,1)=1 AND u.profile_id = 1) )
        LEFT JOIN `restaurant_branches` as rb on (rb.restaurant_id = r.id or rb.id = rb1.id)
        LEFT JOIN `restaurant_branch_keys` as rbs on (rbs.branch_id = rb.id)
        
        WHERE u.`id` = ?
        
        GROUP BY u.`id`;";

         try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            foreach($result as $row)
            {
                $userSecrets =array();
                $profile = Profile::GetProfile(intval($row["profile_id"]),strval($row["profile"]));
                $this->user = User::GetUser(
                    intval($row["id"]),
                    strval($row["email"]),
                    strval($row["user_name"]),
                    strval($row["full_name"]),
                    strval($row["password"]),
                    strval($row["secret_key"]),
                    $profile
                    , array()
                    , array());
                $restaurants = array();
                 $rests = explode( '$',$row["restaurants"]);
                foreach($rests as $rest)
                {
                    $r = explode( ',',$rest);
                    $restaurant = Restaurant::Getrestaurant(intval($r[0])
                    ,strval($r[1])
                    ,strval($r[2])
                    ,strval($r[3])
                    ,strval($r[4])
                    ,strval($r[5])
                    ,strval($r[6])
                    ,array()
                    );
                    $branches = explode( '$',$row["branches"]);
                    foreach($branches as $br)
                    {
                        
                        $bra = explode( ',',$br);
                        if($restaurant->id === intval($bra[0]))
                        {
                            $branch = Branch::GetBranch(
                                intval($bra[0]),
                                intval($restaurant->id),
                                strval($bra[1]),
                                strval($bra[2]),
                                strval($bra[3]),
                                strval($bra[4]),
                                strval($bra[5]),
                                array()
                            );
                            $secrets = explode( '$',$row["secret_keys"]);
                            foreach($secrets as $s)
                            {
                                $secret = explode( ',',$s);
                                if($branch->id === intval($secret[0]))
                                {
                                    array_push($branch->secrets,$secret[1]);
                                    array_push($userSecrets,$secret[1]);
                                }
                            }
                            
                            array_push($restaurant->branches,$branch);
                        }
                    }
                    array_push($restaurants,$restaurant);
                    
                }
                    $this->user->restaurants = $restaurants;
                    $this->user->secrets = $userSecrets;
                    //echo "<li><span> USer => ".json_encode($this->user)."<span></li>";

                    Break;

            }

        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
    function GetUserByUsernamePassword($username,$password)
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