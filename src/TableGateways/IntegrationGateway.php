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
            return $this->Update($input);
        }
    }

    public function Insert(Integration $input): Integration
    {
        $statement = "INSERT INTO $this->tblName
                    (
                    `restaurant_id`,
                    `gf_urid`,
                    `store_id`,
                    `tax_id`,
                    `loyverse_token`)
                    VALUES
                    (:restaurant_id,
                    :gf_urid,
                    :store_id,
                    :tax_id,
                    :loyverse_token);";

        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'restaurant_id' => $input->RestaurantId,
                'gf_urid' => $input->gfUid,
                'loyverse_token' => $input->LoyverseToken,
                'store_id' => $input->StoreId,
                'tax_id' => $input->TaxId,
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
                    `store_id` = :store_id,
                    `tax_id` = :tax_id
                    
                    WHERE `id` = :id;";
        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'restaurant_id' => $input->RestaurantId,
                'gf_urid' => $input->gfUid,
                'store_id' => $input->StoreId,
                'tax_id' => $input->TaxId,
                'loyverse_token' => $input->LoyverseToken,
                'id' => $input->Id,

            ));
            
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
    /**
     * 
     */
    public function InsertOrUpdatePostedType(
        string $categoryName,
        int $gf_id,
        string $type,
        int $integration_id,
        int $gf_menu_id,
        string $loyverse_id,
        int $parent_gf_id = null,
        string $parent_l_id = null,
    ) {


        $statement = "INSERT INTO `tbl_posted_type`
        (`gf_id`,
        `integration_id`,
        `type`,
        `gf_menu_id`,
        `loyverse_id`,
        `name`,
        parent_gfid,
        parent_lid)
        VALUES
        (:gf_id,
        :integration_id,
        :type,
        :gf_menu_id,
        :loyverse_id,
        :name,
        :parent_gfid,
        :parent_lid)
        ON DUPLICATE KEY UPDATE
        `gf_menu_id` = :gf_menu_id,
        `loyverse_id` = :loyverse_id,
        `parent_gfid` = :parent_gfid,
        `parent_lid` = :parent_lid,
        `name` = :name;";
        try {
            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'gf_id' => $gf_id,
                'integration_id' => $integration_id,
                'type' => $type,
                'gf_menu_id' => $gf_menu_id,
                'loyverse_id' => $loyverse_id,
                'name' => $categoryName,
                'parent_gfid' => $parent_gf_id,
                'parent_lid' => $parent_l_id,
            ));

            $this->getDbConnection()->commit();
            return $this->GetTypeByIntegrationAndGfId($gf_id, $integration_id, $type);
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
    /**
     * get 2D array of posted type by menuid and integration id
     */
    public function GetBatchTypeByIntegrationAndMenu(
        int $menu_id,
        int $integration_id
    ) {

        $statment = "SELECT * FROM tbl_posted_type 
        Where `gf_menu_id` = $menu_id AND
        `integration_id` = $integration_id;
        ";

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
                $retval[$value["type"]][$value["gf_id"]] = (object)$value;
            }
            return $retval;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    /**
     * get 2D array of posted type by menuid and integration id
     */
    public function GetBatchTypeByIntegrationAndMenuAndType(
        int $integration_id,
        int $menu_id,
        string $type,
    ) {

        $statment = "SELECT * FROM tbl_posted_type 
        Where `gf_menu_id` = $menu_id AND
        `integration_id` = $integration_id AND
        `type` = '$type';
        ";

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
    /**
     * get 2D array of posted type by menuid and integration id
     */
    public function GetBatchTypeByIntegrationAndType(
        int $integration_id,
        string $type,
    ) {

        $statment = "SELECT * FROM tbl_posted_type 
        Where 
        `integration_id` = $integration_id AND
        `type` = '$type';
        ";

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
        array $name_loyverseId,
        int $parent_gf_id = null,
        string $parent_l_id = null
        /*string $loyverse_id,
    string $categoryName*/
    ) {


        $statement = "INSERT INTO `tbl_posted_type`
        (`gf_id`,
        `integration_id`,
        `type`,
        `gf_menu_id`,
        `loyverse_id`,
        `name`,
        parent_gfid,
        parent_lid)
        VALUES";
        $statements = array();
        foreach ($name_loyverseId as $key => $value) {
            # code...
            //var_dump($value);

            $statements[] = "(" . $value["gf_id"] . ",
                            $integration_id,
                            '$type',
                            $gf_menu_id,
                            '" . $value["l_id"] . "',
                            '" . $value["name"] . "'," 
                            .(isset($parent_gf_id)  && $parent_gf_id != null ? $parent_gf_id : "null" )
                            . ", " 
                            . (isset($parent_l_id) && $parent_l_id != null ? "'$parent_l_id'" : "null")
                . ")";
            //echo $statements;
        }


        $statement .= implode(", ", $statements);
        $statement .= "ON DUPLICATE KEY UPDATE
        `gf_menu_id` = VALUES(`gf_menu_id`),
        `loyverse_id` = VALUES(`loyverse_id`),
        `parent_gfid` = VALUES(`parent_gfid`),
        `parent_lid` = VALUES(`parent_lid`),
        `name` = VALUES(`name`);";
        //echo $statement;
        try {

            $statement = $this->getDbConnection()->prepare($statement);
            $this->getDbConnection()->beginTransaction();
            $statement->execute();

            $this->getDbConnection()->commit();
            $ids = array_column($name_loyverseId, "gf_id");

            return $this->GetBatchTypeByIntegrationAndGfId($ids, $integration_id, $type);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
}
