<?php
namespace Src\Classes;

abstract class ClassObj
{
    // Force Extending class to define this method
    abstract protected function LoadDataObject($data);
    /**  Location for overloaded data.  */
    private $data = array();

    public function getJsonStr()
    {
        return json_encode($this->data);
    }
    public function getJson()
    {
        $jsonStr= json_encode($this->data);
        return \json_decode($jsonStr);
    }
    public function setFromJsonStr(string $jsonString)
    {
        $this->data =  json_decode($jsonString);
    }
    public function __set($name, $value)
    {
        if($name === "data")
        {
            $this->data = $value;
            return;
        }
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }
}