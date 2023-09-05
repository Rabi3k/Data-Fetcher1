<?php

namespace Src\TableGateways;

use Pinq\Caching\NullCache;
use Src\Classes\Integration;
use Src\Classes\Loggy;
use Src\Classes\Restaurant;
use Src\System\DbObject;
use stdClass;

class IntegrationGateway extends DbObject
{
    #region protected functions
    protected function SetSelectStatment()
    {
        $this->selectStatment = "SELECT i.*,r.name as `restaurant_name` FROM $this->tblName i
        left join `tbl_restaurants` r on (i.restaurant_id = r.id)";
    }
    protected function SetTableName()
    {
        $this->tblName = "`tbl_integrations`";
    }
    #endregion
    public function findById(int $id): Integration|Null
    {
        $statment = "  $this->selectStatment Where i.id = $id";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            return Integration::GetIntegration($result[0]);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function findAll(): array
    {
        $statment = " $this->selectStatment ;";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return array();
            }
            return Integration::GetIntegrationList($result);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function findAllByRestaurantIds(array $restaurantIds): array
    {
        $RestaurantIds = implode(",", $restaurantIds);
        $statment = " $this->selectStatment where r.`gf_refid` in($RestaurantIds);";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return array();
            }
            return Integration::GetIntegrationList($result);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function InsertOrupdate(Integration $input)
    {
        if ($input->Id == 0) {
            return $this->Insert($input);
        } else {
            return $this->Insert($input);
        }
    }

    public function Insert(Integration $input): Integration
    {
        $statement = "INSERT INTO $this->tblName
                    (
                    `restaurant_id`,
                    `gf_urid`,
                    `store_id`,
                    `loyverse_token`)
                    VALUES
                    (:restaurant_id,
                    :gf_urid,
                    :store_id,
                    :loyverse_token);";

        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'restaurant_id' => $input->RestaurantId,
                'gf_urid' => $input->gfUid,
                'loyverse_token' => $input->LoyverseToken,
                'store_id' => $input->StoreId,
            ));
            $input->Id = intval($this->getDbConnection()->lastInsertId());
            $this->getDbConnection()->commit();
            return $input;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function Update(Integration $input): Integration
    {
        $statement = "UPDATE $this->tblName
                    SET
                    `restaurant_id` = :restaurant_id,
                    `gf_urid` = :gf_urid,
                    `loyverse_token` = :loyverse_token,
                    `store_id` = :store_id
                    
                    WHERE `id` = :id;";
        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'restaurant_id' => $input->RestaurantId,
                'gf_urid' => $input->gfUid,
                'store_id' => $input->StoreId,
                'loyverse_token' => $input->LoyverseToken,
            ));
            $input->Id = intval($this->getDbConnection()->lastInsertId());
            $this->getDbConnection()->commit();
            return $input;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function GetGfMenuByRestaurantId(int $restaurantId)
    {
        $statment = "  select * from `tbl_gf_menu` Where restaurant_id = $restaurantId";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            return (object)$result[0];
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    public function InsertOrupdateGfMenu(string $menu, int $restaurantId)
    {
        $menuObj = json_decode($menu);

        $statement = "INSERT INTO `tbl_gf_menu`
        (`restaurant_id`,
        `menu_id`,
        `menu`
        )
        VALUES
        (:restaurant_id,
        :menu_id,
        :menu)
          ON DUPLICATE KEY UPDATE
        `menu_id` = :menu_id,
        `menu` = :menu;";
        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'restaurant_id' => $restaurantId,
                'menu_id' => $menuObj->id,
                'menu' => $menu,
            ));

            $this->getDbConnection()->commit();
            return $this->GetGfMenuByRestaurantId($restaurantId);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function GetTypeByIntegrationAndGfId(
        int $gf_category_id,
        int $integration_id,
        string $type
    ) {
        $statment = "SELECT * FROM tbl_posted_type 
        Where `gf_id` = $gf_category_id AND
        `integration_id` = $integration_id AND
        `type` = '$type' ";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            return (object)$result[0];
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    public function InsertOrUpdatePostedType(
        string $categoryName,
        int $gf_category_id,
        string $type,
        int $integration_id,
        int $gf_menu_id,
        string $loyverse_id,
    ) {


        $statement = "INSERT INTO `tbl_posted_type`
        (`gf_id`,
        `integration_id`,
        `type`,
        `gf_menu_id`,
        `loyverse_id`,
        `name`)
        VALUES
        (:gf_id,
        :integration_id,
        :type,
        :gf_menu_id,
        :loyverse_id,
        :name)
        ON DUPLICATE KEY UPDATE
        `gf_menu_id` = :gf_menu_id,
        `loyverse_id` = :loyverse_id,
        `name` = :name;";
        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'gf_id' => $gf_category_id,
                'integration_id' => $integration_id,
                'type' => $type,
                'gf_menu_id' => $gf_menu_id,
                'loyverse_id' => $loyverse_id,
                'name' => $categoryName,
            ));

            $this->getDbConnection()->commit();
            return $this->GetTypeByIntegrationAndGfId($gf_category_id, $integration_id, $type);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage() . ", $categoryName");
        }
    }
    public function GetBatchTypeByIntegrationAndGfId(
        array $gf_id,
        int $integration_id,
        string $type
    ) {
        $catIds = implode(",", $gf_id);

        $statment = "SELECT * FROM tbl_posted_type 
        Where `gf_id` in ( $catIds) AND
        `integration_id` = $integration_id AND
        `type` = '$type' ";

        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            $retval = array();
            foreach ($result as $key => $value) {
                # code...
                $retval[$value["gf_id"]] = (object)$value;
            }
            return $retval;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    public function InsertOrUpdateBatchPostedType(
        //int $gf_id,
        string $type,
        int $integration_id,
        int $gf_menu_id,
        array $name_loyverseId
        /*string $loyverse_id,
    string $categoryName*/
    ) {


        $statement = "INSERT INTO `tbl_posted_type`
        (`gf_id`,
        `integration_id`,
        `type`,
        `gf_menu_id`,
        `loyverse_id`,
        `name`)VALUES";
        $statements = array();
        $values = array();
        foreach ($name_loyverseId as $key => $value) {
            # code...
            $statements[] = "
                            (:gf_id,
                            :integration_id,
                            :type,
                            :gf_menu_id,
                            :loyverse_id,
                            :name)";
                            $values2  = array(
                                'gf_id' => $value["gf_id"],
                                'integration_id' => $integration_id,
                                'type' => $type,
                                'gf_menu_id' => $gf_menu_id,
                                'loyverse_id' => $value["loyverse_id"],
                                'name' => $value["categoryName"],
                            );
                            $values = array_merge($values,$values2);
        }


        $statement .=implode(", ",$statements);
        $statement .= "ON DUPLICATE KEY UPDATE
        `gf_menu_id` = :gf_menu_id,
        `loyverse_id` = :loyverse_id,
        `name` = :name;";
        //echo $statement;
        try {

            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute($values);

            $this->getDbConnection()->commit();
            $ids = array_column($name_loyverseId,"gf_id");
            return $this->GetBatchTypeByIntegrationAndGfId($ids, $integration_id, $type);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
}
