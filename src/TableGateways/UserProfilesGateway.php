<?php

namespace Src\TableGateways;

use Src\Classes\Profile;

use Pinq\Traversable;

class UserProfilesGateway
{
    private $db = null;
    private $tblName = "`tbl_profiles`";

    public function __construct($db)
    {
        $this->db = $db;
    }
    function GetAllProfiles()
    {
        $statement = "SELECT `id`,
        `name`,
        `admin`,
        `super-admin`
        FROM `tbl_profiles`;";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $profiles = array();
            foreach ($result as $row) {
                $profile = Profile::GetProfile(
                    intval($row["id"]),
                    strval($row["name"]),
                    boolval($row["super-admin"]),
                    boolval($row["admin"])
                );
                array_push($profiles, $profile);
            }
            return $profiles;
        } catch (\PDOException $e) {

            exit($e->getMessage());
        }
    }
    function GetProfile($id)
    {
        $statement = "SELECT `id`,
        `name`,
        `admin`,
        `super-admin`
        FROM `tbl_profiles`
        WHERE id = :id
        ;";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array("id"=>intval($id)));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $profiles = array();
            foreach ($result as $row) {
                $profile = Profile::GetProfile(
                    intval($row["id"]),
                    strval($row["name"]),
                    boolval($row["super-admin"]),
                    boolval($row["admin"])
                );
                array_push($profiles, $profile);
            }
            return $profiles;
        } catch (\PDOException $e) {

            exit($e->getMessage());
        }
    }
    function InsertOrUpdate(Profile $input)
    {
        if ($input->id === 0) {
            return $this->InsertProfile($input);
        } else {
            return $this->UpdateProfile($input);
        }
    }
    private function InsertProfile(Profile $input)
    {
        //Password(:password), sha(:secret_key)
        $statement = "INSERT INTO $this->tblName
        (`name`,`admin`,`super-admin`)
        VALUES 
        (:name, :admin, :super_admin)";

        try {
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array(
                'name' => $input->name,
                'admin' => $input->isAdmin,
                'super_admin' => $input->isSuperAdmin
            ));
            $input->id = intval($this->db->lastInsertId());
            $this->db->commit();

            return $input;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    private function UpdateProfile(Profile $input)
    {
        //Password(:password), sha(:secret_key)
        $statement = "UPDATE $this->tblName
         SET 
         `name` =   :name ,
         `admin` =   :admin ,
         `super-admin` =   :super_admin

         WHERE id   = :id;";

        try {
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array(
                'id' => (int)$input->id,
                'name' => $input->name,
                'admin' => $input->isAdmin,
                'super_admin' => $input->isSuperAdmin
            ));
            $this->db->commit();
            return $input;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}
