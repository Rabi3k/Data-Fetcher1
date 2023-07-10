<?php
namespace Src\TableGateways;

use Pinq\Queries\Segments\Select;
use Src\Classes\Options;
use Pinq\Traversable;
use Src\Classes\Loggy;

class OptionsGateway
{
    private $db = null;
    private $tblName = "`tbl_options`";
/**
 * 
 */
    public function __construct($db)
    {
        $this->db = $db;
    }
/**
 * 
 */
    public function findAll()
    {
        $statement = "SELECT * FROM $this->tblName;";

        try {
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array());
            $this->db->commit();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $opTraversable=Traversable::from($result);
            $options = $opTraversable->select(function($x){
                return Options::GetOptions(
                $x["type"],
                $x["name"],
                $x["value"],
            );
            })->asArray();
            return $options;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function findByType($type)
    {
        $statement = "SELECT * FROM $this->tblName where `type` = :type;";

        try {
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array('type'=>$type));
            $this->db->commit();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $opTraversable=Traversable::from($result);
            $options = $opTraversable->select(function($x){
                return Options::GetOptions(
                $x["type"],
                $x["name"],
                $x["value"],
            );
            })->asArray();
            return $options;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
    public function InsertOrUpdate(array $input)
    {
        $values = array();
        foreach ($input as $value) {
            $val = "('$value->type','$value->name','$value->value')";
            array_push($values,$val);
        }
        $valStr= implode(",",$values);
        $statement = "INSERT INTO `tbl_options`
        (`type`,
        `name`,
        `value`)
        VALUES 
        $valStr
        ON DUPLICATE KEY UPDATE
            `value` = VALUES(`value`);";
           try {
           $statement = $this->db->prepare($statement);
           $this->db->beginTransaction();
           $statement->execute(array());
           $this->db->commit();
           return $input;
        } catch (\PDOException $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            exit($e->getMessage());
        }
    }
}