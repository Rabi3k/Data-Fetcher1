<?php
namespace Src\System;

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
    private function Select()
    {
        {
            $statement = "$this->selectStatment";
    
            try {
                $statement = $this->db->query($statement);
                $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
                return $result;
            } catch (\PDOException $e) {
                exit($e->getMessage());
            }
        }
    }
    
    #endregion
}