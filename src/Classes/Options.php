<?php

namespace Src\Classes;

class Options
{
    public string $type; //String
    public string $name; //string
    public string $value; //string


    #region public functions
    public function LoadOptions(
        string $type,
        string $name,
        string $value
    ) {

        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
    }

    #endregion
    #region static function

    public static function GetOptions(
        string $type,
        string $name,
        string $value
    ) {
        $option = new Options();
        $option->LoadOptions(
            $type,
            $name,
            $value
        );
        //echo json_encode($restaurant);
        return $option;
    }
    public static function classToArray(array $arrayClass)
    {
        $retval = array();
        foreach ($arrayClass as $value) {
            $retval[$value->name] = $value->value;
        }
        return $retval;
    }
    public static function arrayToClass(array $arrayClass, $type)
    {
        $retval = array();
        foreach ($arrayClass as $key => $value) {
            $op = Options::GetOptions(
                $type,
                $key,
                $value
            );
            array_push($retval,$op);
        }
        return $retval;
    }
    #endregion
}
