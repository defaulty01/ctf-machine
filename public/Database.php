<?php

class Database 
{
    private $host;
    private $user;
    private $pass;
    private $dbName;

    private static $database = null;
    
    private $mysql;

    public function __construct($host, $user, $pass, $dbName)
    {
        $this->host     = $host;
        $this->user     = $user;
        $this->pass     = $pass;
        $this->dbName   = $dbName;

        self::$database = $this;
    }

    public static function getDatabase(): Database
    {
        return self::$database;
    }

    public function connect()
    {
        $this->mysql = new mysqli($this->host, $this->user, $this->pass, $this->dbName);
    }


    public function query($query, $params = [], $return = true)
    {
        $types = "";
        $finalParams = [];

        if($params !== []){
            foreach ($params as $key => $value)
            {
                $types .= str_repeat($key, count($value));
                $finalParams = array_merge($finalParams, $value);
            }

            $stmt = $this->mysql->prepare($query);
            $stmt->bind_param($types, ...$finalParams);

            if (!$stmt->execute())
            {
                return false;
            }

            if (!$return)
            {
                return true;
            }

            return $stmt->get_result() ?? false;
        }else{
            $result = $this->mysql->query($query);
            if($this->mysql->error){
                return false;
            }
            #var_dump($result->fetch_assoc());
            return $result->fetch_assoc() ?? false;
            
        }

    }
}