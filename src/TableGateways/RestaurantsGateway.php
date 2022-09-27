<?php

namespace Src\TableGateways;

use Pinq\Traversable;

use Src\Classes\Branch;
use Src\Classes\Restaurant;

class RestaurantsGateway
{
    private $db = null;
    private $tblName = "`restaurants`";

    public function __construct($db)
    {
        $this->db = $db;
    }
    function GetAllRestaurants()
    {
        $statement = "SELECT `id`,
        `name`,
        `phone`,
        `email`,
        `cvr`,
        `logo`,
        `reference_id`
    FROM $this->tblName;";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $restaurants = array();
            foreach ($result as $row) {
                $restaurant = Restaurant::GetRestaurant(
                    intval($row["id"]),
                    strval($row["email"]),
                    strval($row["name"]),
                    strval($row["phone"]),
                    strval($row["cvr"]),
                    strval($row["logo"]),
                    strval($row["reference_id"]),
                    array()
                );
                array_push($restaurants, $restaurant);
            }
            return $restaurants;
        } catch (\PDOException $e) {

            exit($e->getMessage());
        }
    }
    function GetRestaurant($id)
    {
        $statement = "SELECT 
        r.`id`,
        r.`name`,
        r.`phone`,
        r.`email`,
        r.`cvr`,
        r.`logo`,
        r.`reference_id`,
        
        -- restaurant_branches
        CONCAT('[',
            GROUP_CONCAT(DISTINCT JSON_OBJECT(
                'id', rb.`id`,
                'restaurant_id', rb.`restaurant_id`,
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
    FROM
        `restaurants` r
            LEFT JOIN
        `restaurant_branches` rb ON (r.`id` = rb.restaurant_id)
            LEFT JOIN
        `restaurant_branch_keys` AS rbs ON (rbs.branch_id = rb.id)
    WHERE
        r.`id` = :id
    GROUP BY r.`id`
    ;";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array("id" => intval($id)));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $restaurants = array();
            foreach ($result as $row) {
                $restaurant = Restaurant::GetRestaurant(
                    intval($row["id"]),
                    strval($row["email"]),
                    strval($row["name"]),
                    strval($row["phone"]),
                    strval($row["cvr"]),
                    strval($row["logo"]),
                    strval($row["reference_id"]),
                    array()
                );
                $branches = json_decode($row["branches"]);
                
                foreach ($branches as $br) {
                    if(isset($br->restaurant_id) && $br->restaurant_id === $restaurant->id)
                    {
                        $branch = Branch::GetBranch(
                            $br->id,
                            $br->restaurant_id,
                            $br->city,
                            $br->zip_code,
                            $br->address,
                            $br->country,
                            $br->cvr,
                            array()
                        );
                        $secrets =  json_decode($row["secret_keys"]);
                        foreach ($secrets as $s) {
                            if ($branch->id === $s->branch_id) {
                                array_push($branch->secrets, $s->secret_key);
                            }
                        }

                        array_push($restaurant->branches, $branch);
                    }
                   
                }
                
                array_push($restaurants, $restaurant);
            }
            return $restaurants;
        } catch (\PDOException $e) {

            exit($e->getMessage());
        }
    }
    function InsertOrUpdate(Restaurant $input)
    {
        if ($input->id === 0) {
            return $this->InsertRestaurant($input);
        } else {
            return $this->UpdateRestaurant($input);
        }
    }
    private function InsertRestaurant(Restaurant $input)
    {
        //Password(:password), sha(:secret_key)
        $statement = "INSERT INTO $this->tblName
        (`name`,
        `phone`,
        `email`,
        `cvr`,
        `logo`,
        `reference_id`)
        VALUES 
        (:name,
        :phone,
        :email,
        :cvr,
        :logo,
        :reference_id);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $st = $statement->execute(array(
                'name' => $input->name,
                'phone' => $input->phone,
                'email' => $input->email,
                'cvr' => $input->cvr,
                'logo' => $input->logo,
                'reference_id' => $input->reference_id
            ));
            $input->id = intval($this->db->lastInsertId());
            $this->db->commit();

            return $input;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    private function UpdateRestaurant(Restaurant $input)
    {
        //Password(:password), sha(:secret_key)
        $statement = "UPDATE $this->tblName
         SET 
         `name` =   :name ,
         `phone` =   :phone ,
         `email` =   :email ,
         `cvr` =   :cvr ,
         `logo` =   :logo ,
         `reference_id` =   :reference_id

         WHERE id   = :id;";

        try {
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array(
                'id' => (int)$input->id,
                'name' => $input->name,
                'phone' => $input->phone,
                'email' => $input->email,
                'cvr' => $input->cvr,
                'logo' => $input->logo,
                'reference_id' => $input->reference_id
            ));
            $this->db->commit();
            return $input;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    function InsertOrUpdateBranch(Branch $input)
    {
        if (!isset($input->id)||$input->id === 0) {
            return $this->InsertBranch($input);
        } else {
            return $this->UpdateBranch($input);
        }
    }
    private function InsertBranch(Branch $input)
    {
        //Password(:password), sha(:secret_key)
        $statement = "INSERT INTO `restaurant_branches`
        (`restaurant_id`,
        `city`,
        `zip_code`,
        `address`,
        `country`,
        `cvr`)
        VALUES 
        (:restaurant_id,
        :city,
        :zip_code,
        :address,
        :country,
        :cvr);";

        try {
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array(
                'restaurant_id' => $input->restaurantId,
                'city' => $input->city,
                'zip_code' => $input->zip_code,
                'cvr' => $input->cvr,
                'address' => $input->address,
                'country' => $input->country
            ));
            $input->id = intval($this->db->lastInsertId());
            $this->db->commit();

            return $input;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    private function UpdateBranch(Branch $input)
    {
        //Password(:password), sha(:secret_key)
        $statement = "UPDATE `restaurant_branches`
         SET 
         `restaurant_id` =   :restaurant_id ,
         `city` =   :city ,
         `zip_code` =   :zip_code ,
         `cvr` =   :cvr ,
         `address` =   :address ,
         `country` =   :country

         WHERE id   = :id;";

        try {
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array(
                'id' => (int)$input->id,
                'restaurant_id' => $input->restaurantId,
                'city' => $input->city,
                'zip_code' => $input->zip_code,
                'cvr' => $input->cvr,
                'address' => $input->address,
                'country' => $input->country
            ));
            $this->db->commit();
            return $input;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function InsertOrUpdateBranchSecrets(Branch $input)
    {
//Password(:password), sha(:secret_key)
$Dstatment = "DELETE FROM `kbslb_portal`.`restaurant_branch_keys`
WHERE `branch_id` = :branch_id;";

$Istatement = "INSERT INTO `restaurant_branch_keys`
(`branch_id`,
`secret_key`)
VALUES
";
$secsStatment = array();
foreach($input->secrets as $secret)
{
    $secStatment="($input->id,'$secret')";
    array_push($secsStatment,$secStatment);
}
$Istatement .=implode(",",$secsStatment).";";
try {
    $statement = $this->db->prepare("$Dstatment");
    $this->db->beginTransaction();
    $statement->execute(array(
        'branch_id' => $input->id
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
}
