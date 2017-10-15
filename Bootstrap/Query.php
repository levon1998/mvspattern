<?php

class Query
{
    private $db = null;
    public function __construct()
    {
        $configs = require_once(ROOT.'/Conf.php');
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

    public function insert()
    {

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

    public function queryToDb($sql)
    {
        sleep(1);
        $db = $this->db;
        if ($db->query("$sql")) {
            echo "Table 'migrations' is created successfull! \n";
        } else {
            return "Table migration is not creating \n";
        }


    }
}