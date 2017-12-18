<?php

class Query extends App
{
    private $db = null;
    public function __construct()
    {
        $configs = require(ROOT.'/Conf.php');
        require_once (ROOT.'/Components/Configs.php');

        $conf = new Configs();
        $this->db = $conf->dbConfigs($configs);
    }

    public function getAllTables()
    {
        $db = $this->db;
        $rows = $db->query("SHOW TABLES;")->fetchAll(PDO::FETCH_COLUMN, 0);
        $tables = [];
        if ($rows) {
            foreach ($rows as $row) {
                $tables[$row] = $row;
            }
        }
        return $tables;
    }

    public function getTable($name)
    {
        $tables = $this->getAllTables();
        return (isset($tables[$name])) ? true : false;
    }

    public function findAll($table)
    {
        if ($table) {
            $sql = 'SELECT * FROM '.$table;
            return $this->queryToDb($sql);
        } else {
            self::getInstance()->error("Table name is incorrect!");
        }
    }

    public function insert($table, $data = null)
    {
        if ($table || $data) {
            $db = $this->db;
            $column = [];
            $placeHolders = [];
            if ($data && count($data) > 0) {
                foreach ($data as $value) {
                    foreach ($value as $key => $item) {
                        $column[] = "`".$key."`";
                        $placeHolders[] = ":$key";
                    }
                }
                $column = array_unique($column);
                $placeHolders = array_unique($placeHolders);

                $column       = implode(', ', $column);
                $placeHolders = implode(', ', $placeHolders);

                $sql = "INSERT INTO `".$table."` ($column) VALUES ($placeHolders);";
                $query = $db->prepare($sql);
                foreach ($data as $value) {
                    foreach ($value as $key => $item) {
                        $query->bindValue(":$key", $item);
                    }
                }
                return $query->execute();
            }
        } else {
            self::getInstance()->error("Invalid data !");
        }
    }

    protected function delete()
    {

    }

    protected function find()
    {

    }

    public function createTable()
    {

    }

    public function addColumn()
    {

    }

    public function dropColumn()
    {

    }

    public function emptyTable()
    {

    }

    public function dropTable()
    {

    }

    public function update()
    {

    }

    public function where()
    {

    }

    public function queryToDb($sql)
    {
        $db = $this->db;
        try {
            $result = $db->query($sql);
            return $result;
        }catch (Exception $e) {
            self::getInstance()->error($e);
        }
    }
}