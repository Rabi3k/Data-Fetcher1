<?php

namespace Src\TableGateways;

use Src\Classes\Loggy;
use Src\Classes\Restaurant;
use Src\System\DbObject;

class RestaurantsGateway extends DbObject
{

    protected function SetSelectStatment()
    {
        $this->selectStatment = "SELECT * FROM $this->tblName;";
    }
    protected function SetTableName()
    {
        $this->tblName = "`tbl_restaurants`";
    }
    #endregion
    public function FindById($id): Restaurant|null
    {
        $tblname = $this->getTableName();
        $statment = "SELECT * FROM $tblname 
        where id = $id";
        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            return Restaurant::GetRestaurant($result[0]);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function FindByCompanyId($companyId): array|null
    {
        $tblname = $this->getTableName();
        $statment = "SELECT * FROM $tblname 
        where company_id = $companyId";
        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            return Restaurant::GetRestaurantList($result);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function GetAll(): array|null
    {
        try {
            $statement = $this->getDbConnection()->query($this->selectStatment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return array();
            }
            return Restaurant::GetRestaurantList($result);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }


    function InsertOrUpdate(Restaurant $input)
    {
        if ($input->id == 0) {
            return $this->InsertRestaurant($input);
        } else {
            return $this->InsertOrUpdateRestaurant($input);
        }
    }
    private function InsertRestaurant(Restaurant $input)
    {
        $statement = "INSERT INTO $this->tblName
        (
        `company_id`,
        `name`,
        `alias`,
        `p_nr`,
        `address`,
        `post_nr`,
        `city`,
        `email`,
        `phone`,
        `is_gf`,
        `is_managed`,
        `gf_refid`,
        `gf_urid`,
        `gf_cdn_base_path`)
        VALUES
        (
        :company_id,
        :name,
        :alias,
        :p_nr,
        :address,
        :post_nr,
        :city,
        :email,
        :phone,
        :is_gf,
        :is_managed,
        :gf_refid,
        :gf_urid,
        :gf_cdn_base_path)";


        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'company_id' => $input->company_id,
                'name' => $input->name,
                'alias' => $input->alias,
                'p_nr' => $input->p_nr,
                'address' => $input->address,
                'post_nr' => $input->post_nr,
                'city' => $input->city,
                'email' => $input->email,
                'phone' => $input->phone,
                'is_gf' => $input->is_gf,
                'is_managed' => $input->is_managed,
                'gf_refid' => $input->gf_refid,
                'gf_urid' => $input->gf_urid,
                'gf_cdn_base_path' => $input->gf_cdn_base_path,
            ));
            $input->id = intval($this->getDbConnection()->lastInsertId());
            $this->getDbConnection()->commit();

            return $input;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    private function InsertOrUpdateRestaurant(Restaurant $input)
    {
        $statement = "INSERT INTO $this->tblName
        (`id`,
        `company_id`,
        `name`,
        `alias`,
        `p_nr`,
        `address`,
        `post_nr`,
        `city`,
        `email`,
        `phone`,
        `is_gf`,
        `is_managed`,
        `gf_refid`,
        `gf_urid`,
        `gf_cdn_base_path`)
        VALUES
        (:id,
        :company_id,
        :name,
        :alias,
        :p_nr,
        :address,
        :post_nr,
        :city,
        :email,
        :phone,
        :is_gf,
        :is_managed,
        :gf_refid,
        :gf_urid,
        :gf_cdn_base_path)
          ON DUPLICATE KEY UPDATE
        `company_id` = :company_id,
        `name` = :name,
        `alias` = :alias,
        `p_nr` = :p_nr,
        `address` = :address,
        `post_nr` = :post_nr,
        `city` = :city,
        `email` = :email,
        `phone` = :phone,
        `is_gf` = :is_gf,
        `is_managed` = :is_managed,
        `gf_refid` = :gf_refid,
        `gf_urid` = :gf_urid,
        `gf_cdn_base_path` = :gf_cdn_base_path;";

        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'id' => $input->id,
                'company_id' => $input->company_id,
                'name' => $input->name,
                'alias' => $input->alias,
                'p_nr' => $input->p_nr,
                'address' => $input->address,
                'post_nr' => $input->post_nr,
                'city' => $input->city,
                'email' => $input->email,
                'phone' => $input->phone,
                'is_gf' => $input->is_gf,
                'is_managed' => $input->is_managed,
                'gf_refid' => $input->gf_refid,
                'gf_urid' => $input->gf_urid,
                'gf_cdn_base_path' => $input->gf_cdn_base_path,
            ));

            $insertid = intval($this->getDbConnection()->lastInsertId());
            if ($insertid > 0) {
                $input->id = $insertid;
            }
            $this->getDbConnection()->commit();

            return $input;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    // function InsertOrUpdateBranch(Branch $input)
    // {
    //     if (!isset($input->id) || $input->id === 0) {
    //         return $this->InsertBranch($input);
    //     } else {
    //         return $this->UpdateBranch($input);
    //     }
    // }
    // private function InsertBranch(Branch $input)
    // {
    //     //Password(:password), sha(:secret_key)
    //     $statement = "INSERT INTO `restaurant_branches`
    //     (`restaurant_id`,
    //     `city`,
    //     `zip_code`,
    //     `address`,
    //     `country`,
    //     `cvr`)
    //     VALUES 
    //     (:restaurant_id,
    //     :city,
    //     :zip_code,
    //     :address,
    //     :country,
    //     :cvr);";

    //     try {
    //         $statement = $this->db->prepare($statement);
    //         $this->db->beginTransaction();
    //         $statement->execute(array(
    //             'restaurant_id' => $input->restaurantId,
    //             'city' => $input->city,
    //             'zip_code' => $input->zip_code,
    //             'cvr' => $input->cvr,
    //             'address' => $input->address,
    //             'country' => $input->country
    //         ));
    //         $input->id = intval($this->db->lastInsertId());
    //         $this->db->commit();

    //         return $input;
    //     } catch (\PDOException $e) {
    //         exit($e->getMessage());
    //     }
    // }
    // private function UpdateBranch(Branch $input)
    // {
    //     //Password(:password), sha(:secret_key)
    //     $statement = "UPDATE `restaurant_branches`
    //      SET 
    //      `restaurant_id` =   :restaurant_id ,
    //      `reference_id` =   :reference_id ,
    //      `city` =   :city ,
    //      `zip_code` =   :zip_code ,
    //      `cvr` =   :cvr ,
    //      `address` =   :address ,
    //      `country` =   :country

    //      WHERE id   = :id;";

    //     try {
    //         $statement = $this->db->prepare($statement);
    //         $this->db->beginTransaction();
    //         $statement->execute(array(
    //             'id' => (int)$input->id,
    //             'restaurant_id' => $input->restaurantId,
    //             'city' => $input->city,
    //             'zip_code' => $input->zip_code,
    //             'cvr' => $input->cvr,
    //             'address' => $input->address,
    //             'country' => $input->country,
    //             'reference_id'=>$input->reference_id
    //         ));
    //         $this->db->commit();
    //         return $input;
    //     } catch (\PDOException $e) {
    //         exit($e->getMessage());
    //     }
    // }

    // public function InsertOrUpdateBranchSecrets(Branch $input)
    // {
    //     //Password(:password), sha(:secret_key)
    //     $Dstatment = "DELETE FROM `restaurant_branch_keys`
    //         WHERE `branch_id` = :branch_id;";

    //                 $Istatement = "INSERT INTO `restaurant_branch_keys`
    //         (`branch_id`,
    //         `secret_key`)
    //         VALUES
    //         ";
    //     $secsStatment = array();
    //     foreach ($input->secrets as $secret) {
    //         $secStatment = "($input->id,'$secret')";
    //         array_push($secsStatment, $secStatment);
    //     }
    //     $Istatement .= implode(",", $secsStatment) . ";";
    //     try {
    //         $statement = $this->db->prepare("$Dstatment");
    //         $this->db->beginTransaction();
    //         $statement->execute(array(
    //             'branch_id' => $input->id
    //         ));
    //         $this->db->commit();
    //         $statement = $this->db->prepare("$Istatement");
    //         $this->db->beginTransaction();
    //         $statement->execute(array());
    //         $this->db->commit();

    //         return true;
    //     } catch (\PDOException $e) {
    //         exit($e->getMessage());
    //     }
    // }
}
