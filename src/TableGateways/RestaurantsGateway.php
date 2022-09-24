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
        GROUP_CONCAT(DISTINCT CONCAT(rb.`id`,
                    ',',
                    rb.`restaurant_id`,
                    ',',
                    rb.`city`,
                    ',',
                    rb.`zip_code`,
                    ',',
                    rb.`address`,
                    ',',
                    rb.`country`,
                    ',',
                    IFNULL(rb.`cvr`, '0'))
            SEPARATOR '$') AS 'branches',

            -- restaurant_branch_keys
        GROUP_CONCAT(DISTINCT CONCAT(rbs.`branch_id`, ',', rbs.`secret_key`)
            SEPARATOR '$') AS 'secret_keys'
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
                $branches = explode('$', $row["branches"]);
                foreach ($branches as $br) {

                    $bra = explode(',', $br);
                    if (isset($bra[1]) && $restaurant->id === intval($bra[1])) {
                        $branch = Branch::GetBranch(
                            intval($bra[0]),
                            intval($restaurant->id),
                            strval($bra[2]),
                            strval($bra[3]),
                            strval($bra[4]),
                            strval($bra[5]),
                            strval($bra[6]),
                            array()
                        );
                        $secrets = explode('$', $row["secret_keys"]);
                        foreach ($secrets as $s) {
                            $secret = explode(',', $s);
                            if ($branch->id === intval($secret[0])) {
                                array_push($branch->secrets, $secret[1]);
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
        :reference_id)";

        try {
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array(
                'name' => $input->name,
                'phone' => $input->phone,
                'email' => $input->email,
                'cvr' => $input->cvr,
                'logo' => $input->logo,
                'reference_id' => $input->reference_id
            ));
            $this->db->commit();
            $input->id = intval($this->db->lastInsertId());
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
}
