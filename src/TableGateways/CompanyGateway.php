<?php

namespace Src\TableGateways;

use Pinq\Traversable;
use Src\Classes\Company;
use Src\Classes\Loggy;
use Src\Classes\Order;
use Src\Enums\ItemStatus;
use Src\System\DbObject;

class CompanyGateway extends DbObject
{
    #region protected functions
    protected function SetSelectStatment()
    {
        $this->selectStatment = "SELECT * FROM $this->tblName;";
    }
    protected function SetTableName()
    {
        $this->tblName = "`tbl_companies`";
    }
    #endregion
    #region public functions
    public function FindById($id): Company|null
    {
        $tblname = $this->getTableName();
        $statment = "SELECT json_object(
            'id',c.`id`,
            'name',    c.`name`,
                'cvr_nr',c.`cvr_nr`,
                'address',c.`address`,
                'city',c.`city`,
                'zip',c.`zip`,
                'email',c.`email`,
                'phone',c.`phone`,
                'gf_refid',c.`gf_refid`,
                    'restaurants',case when r.`id` is NULL THEN JSON_ARRAY()
                     ELSE JSON_ARRAYAGG(DISTINCT 
                     JSON_OBJECT( 'id', r.`id`, 
                     'company_id', r.`company_id`, 
                     'name', r.`name`, 
                     'alias', r.`alias`, 
                     'p_nr', r.`p_nr`, 
                     'address', r.`address`, 
                     'city', r.`city`, 
                     'post_nr', r.`post_nr`, 
                     'country', r.`country`,
                     'email', r.`email`,
                     'phone', r.`phone`, 
                     'is_gf',  convert(r.`is_gf`,int), 
                     'is_managed', convert(r.`is_managed`,int), 
                     'gf_refid', r.`gf_refid`,
                     'gf_urid', convert(r.`gf_urid`,varchar(64))
                     
                     ) ) END ,
                            'restaurants_count',count(r.id))as 'company'

        FROM $tblname c
        left join tbl_restaurants r on (c.id = r.company_id) 
        where c.id = $id
        group by c.id,r.company_id
        ";
        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            //var_dump($result[0]["company"]);
            return Company::GetCompany($result[0]["company"]);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function GetJsonCompanyTreeByCompanyIDs(array $companyIds): array
    {
        $tblname = $this->getTableName();
        $statment = 'SELECT json_object(
            "id",c.`id`,
            "name",    c.`name`,
                "cvr_nr",c.`cvr_nr`,
                "address",c.`address`,
                "city",c.`city`,
                "zip",c.`zip`,
                "email",c.`email`,
                "phone",c.`phone`,
                "gf_refid",c.`gf_refid`,
                    "restaurants",case when r.`id` is NULL THEN JSON_ARRAY()
                     ELSE JSON_ARRAYAGG(DISTINCT 
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
                     "gf_urid", convert(r.`gf_urid`,varchar(64))
                     
                     ) ) END ,
                            "restaurants_count",count(r.id))as "company"
                    FROM tbl_companies c
                    left join tbl_restaurants r on (c.id = r.company_id) 
                    where c.id in(:ids)
                    group by c.id,r.company_id;';
        try {
            $statement = $this->getDbConnection()->query($statment);
            $statement->execute(array("ids" => implode(",", $companyIds)));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            return Company::GetCompaniesJsonList($result);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function GetJsonCompanyTree(): array
    {
        $tblname = $this->getTableName();
        $statment = 'SELECT json_object(
            "id",c.`id`,
            "name",    c.`name`,
                "cvr_nr",c.`cvr_nr`,
                "address",c.`address`,
                "city",c.`city`,
                "zip",c.`zip`,
                "email",c.`email`,
                "phone",c.`phone`,
                "gf_refid",c.`gf_refid`,
                    "restaurants",case when r.`id` is NULL THEN JSON_ARRAY()
                     ELSE JSON_ARRAYAGG(DISTINCT 
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
                     "gf_urid", convert(r.`gf_urid`,varchar(64))
                     
                     ) ) END ,
                            "restaurants_count",count(r.id))as "company"
                    FROM tbl_companies c
                    left join tbl_restaurants r on (c.id = r.company_id) 
                    group by c.id,r.company_id;';
        try {
            $statement = $this->getDbConnection()->query($statment);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            return Company::GetCompaniesJsonList($result);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function GetAllAdvanced(): array|null
    {
        $tblname = $this->getTableName();
        $statment = "SELECT c.*,
        case when r.`id` is NULL THEN JSON_ARRAY()
         ELSE JSON_ARRAYAGG(DISTINCT JSON_OBJECT(
                                    'id', r.`id`,
                                    'company_id', r.`company_id`,
                                    'name', r.`name`,
                                    'alias', r.`alias`,
                                    'p_nr', r.`p_nr`,
                                    'address', r.`address`,
                                    'city', r.`city`,
                                    'post_nr', r.`post_nr`,
                                    'country', r.`country`,
                                    'email', r.`email`,
                                    'phone', r.`phone`,
                                    'is_gf', convert(r.`is_gf`,int), 
                                    'is_managed', convert(r.`is_managed`,int), 
                                    'gf_refid', r.`gf_refid`,
                                    'gf_urid', r.`gf_urid`,
                                    'gf_cdn_base_path', r.`gf_cdn_base_path`)
                                )END  AS 'restaurants',
                                count(r.id) as 'restaurants_count'

                        FROM $tblname c
                        left join tbl_restaurants r on (c.id = r.company_id) 
                        group by c.id,r.company_id";
        //echo "ID: $id <br/>statment: $statment<br/>";
        try {
            $statement = $this->getDbConnection()->query($statment);

            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement->closeCursor();
            if (count($result) < 1) {
                return null;
            }
            return Company::GetCompanyList($result);
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
            return Company::GetCompanyList($result);
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }

    /**
     * Insert or Update Company
     */
    function InsertOrUpdate(Company $input)
    {
        if ($input->id == 0) {
            return $this->InsertCompany($input);
        } else {
            return $this->InsertOrUpdateCompany($input);
        }
    }
    public function InsertCompany(Company $input)
    {
        $statment = "INSERT INTO $this->tblName
        (
        `name`,
        `cvr_nr`,
        `address`,
        `city`,
        `zip`,
        `email`,
        `phone`,
        `gf_refid`)
        VALUES
        (
        :name,
        :cvr_nr,
        :address,
        :city,
        :zip,
        :email,
        :phone,
        :gf_refid);";
        try {
            $statement = $this->getDbConnection()->prepare($statment);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'name' => $input->name,
                'cvr_nr' => $input->cvr_nr,
                'address' => $input->address,
                'city' => $input->city,
                'zip' => $input->zip,
                'email' => $input->email,
                'phone' => $input->phone,
                'gf_refid' => intval($input->gf_refid)
            ));
            $input->id = intval($this->getDbConnection()->lastInsertId());
            $this->getDbConnection()->commit();

            return $input;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function InsertOrUpdateCompany(Company $input)
    {
        $statment = "INSERT INTO $this->tblName
        (`id`,
        `name`,
        `cvr_nr`,
        `address`,
        `city`,
        `zip`,
        `email`,
        `phone`,
        `gf_refid`)
        VALUES
        (:id,
        :name,
        :cvr_nr,
        :address,
        :city,
        :zip,
        :email,
        :phone,
        :gf_refid)        
         ON DUPLICATE KEY UPDATE 
        `name` = :name,
        `cvr_nr` = :cvr_nr,
        `address` = :address,
        `city` = :city,
        `zip` = :zip,
        `email` = :email,
        `phone` = :phone,
        `gf_refid` = :gf_refid;
        ";
        try {
            $statement = $this->getDbConnection()->prepare($statment);
            $this->getDbConnection()->beginTransaction();
            $statement->execute(array(
                'id' => intval($input->id),
                'name' => $input->name,
                'cvr_nr' => $input->cvr_nr,
                'address' => $input->address,
                'city' => $input->city,
                'zip' => $input->zip,
                'email' => $input->email,
                'phone' => $input->phone,
                'gf_refid' => intval($input->gf_refid)
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

    #endregion
}
