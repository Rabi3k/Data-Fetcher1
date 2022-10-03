<?php

namespace Src\TableGateways;

use DateTime;
use Src\Classes\Request;


class RequestsGateway
{

    private $db = null;
    private $tblName = "`requests`";

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "SELECT * FROM $this->tblName;";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    



    public function find($id)
    {
        $statement = "SELECT * FROM $this->tblName WHERE id = ?;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function insertFromClass(Request $input)
    {
        $statement = "INSERT INTO $this->tblName
        (`header`, `body`) 
        VALUES 
        (:header, :body);";
        /*
    public $header; //String
    public $body; //String
    public $created_date; //Date
    public $executed; //int
 */
        try {
            $statement = $this->db->prepare($statement);
            $this->db->beginTransaction();
            $statement->execute(array(
                'header'  => $input->header,
                'body'  => $input->body,
            ));
            $this->db->commit();
            /*  $oDate = new \DateTime();
                $oDate->setTimezone( new \DateTimeZone('Europe/Copenhagen'));
                if (!file_exists("logs/".$oDate->format('dmY'))) {
                    mkdir("logs/".$oDate->format('dmY'), 0777, true);
                }
                $myfile = fopen("logs/".$oDate->format('dmY')."/Log_".$input->order_id.".txt", "w")
                                or die("Unable to open file!");
                $inputStr ="Test 123123:".json_encode($input)."\n\r SqlStatment:".json_encode($statement);
                fwrite($myfile, $inputStr);
                fclose($myfile);*/

            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

   

    public function update($id, array $input)
    {
        $statement = "
            UPDATE $this->tblName
            SET 
            header = :header,
            body  = :body,
            created_date = :created_date,
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'header' => $input['header'] ,
                'body'  => $input['body'],
                'created_date' => $input['created_date'] ?? null,
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM $this->tblName
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}
