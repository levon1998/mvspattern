<?php

class Configs
{
    public function setConstants($configs)
    {
        $errors = 0;
        if ($configs['CONF_ERRORS']) {
            $errors = 1;
        }
        ini_set('display_errors', $errors);
        error_reporting(E_ALL);
    }

    public function dbConfigs($configs)
    {
        $dbType = $configs['DB_TYPE'];
        $dbHost = $configs['DB_HOST'];
        $dbName = $configs['DB_NAME'];
        $dbUser = $configs['DB_USER'];
        $dbPass = $configs['DB_PASS'];
        try{
            $newConnection = new PDO("$dbType:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        return $newConnection;
    }
}