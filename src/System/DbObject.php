<?php
namespace Src\System;

use Src\Classes\Loggy;

abstract class DbObject
{
    #region Private Properties
    private $db = null;
    protected string  $tblName, 
                    $selectStatment;
    #endregion

    #region Constructor
    public function __construct($db)
    {
        $this->db   =   $db;
        $this->SetTableName();
        $this->SetSelectStatment();
    }
    #endregion

    #region abstract functions
    abstract protected function SetSelectStatment();
    abstract protected function SetTableName();
    #endregion



    
    #region Protected functions
    protected function getDbConnection()
    {return$this->db;}
    protected function getTableName()
    {return$this->tblName;}
    #endregion


    #region private functions
    public function SelectAll()
    {
        {
            $statement = "$this->selectStatment";
    
            try {
                $statement = $this->db->query($statement);
                $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
                return $result;
            } catch (\PDOException $e) {
                (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
                exit($e->getMessage());
            }
        }
    }
    
    #endregion
    #region Static Functions
    
    #endregion
}