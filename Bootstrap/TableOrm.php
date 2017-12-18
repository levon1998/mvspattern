<?php

class TableOrm extends App
{
    public $table;
    protected static $tableQuery = '';
    protected $columns = [];
    protected $operation = [];

    private $db = null;
    public function __construct()
    {
        $configs = require(ROOT.'/Conf.php');
        require_once (ROOT.'/Components/Configs.php');
        $conf = new Configs();
        $this->db = $conf->dbConfigs($configs);
    }

    public static function create($name)
    {
        if (self::checkName($name)) {
            self::$tableQuery .= "CREATE TABLE $name ( ";
            return new self();
        } else {
            return false;
        }
    }

    public function increment($name, $lenght = 11)
    {
        if (self::checkName($name)) {
            return $this->unsigned($name, $lenght, true);
        } else {
            return false;
        }
    }

    private function unsigned($name, $lenght, $increment)
    {
        return $this->primary($name, $lenght, $increment, true);
    }

    protected function primary($name, $lenght, $increment, $unsigned)
    {
        return $this->intager($name, $lenght, $increment, $unsigned, true);
    }

    public function intager($name, $lenght, $increment = false, $unsigned = false, $primary = false)
    {
        if (self::checkName($name)) {
            $string = "$name INT($lenght) ";
            if ($unsigned) {
                $string .= "UNSIGNED ";
            }
            if ($increment) {
                $string .= "AUTO_INCREMENT ";
            }
            if ($primary) {
                $string .= "PRIMARY KEY ";
            }
            self::$tableQuery .= $string.",";
            return new self();
        } else {
            return false;
        }
    }

    public function string($name, $null = true, $lenght = 191)
    {
        if (self::checkName($name)) {
            $string = "$name VARCHAR($lenght) ";
            if ($null) {
                $string .= 'NULL ';
            } else {
                $string .= 'NOT NULL ';
            }
            self::$tableQuery .= $string;
            return new self();
        } else {
            return false;
        }
    }

    public function make()
    {
        $query = self::$tableQuery . ");";
        if ($this->queryToDb($query))
        {
            return true;
        }
    }

    private static function checkColumns($name, $params)
    {
        if ($params) {
            return self::getColumns($name, $params);
        } else {
            self::error("The table fields is not found");
        }
    }

    private static function validationForFields($param)
    {
        $allowMetods = ['name', 'type', 'length', 'increment', 'unsigned', 'nullabel', 'default', 'primary_key'];
        if ($param) {
            foreach ($param as $key => $val) {
                if (!in_array($key, $allowMetods)) {
                    self::error("Table have an invalid parameter");
                }
                if (!self::checkTypeFields($key, $val)) {
                    self::error("Table Field value is not currect");
                }
            }
        } else {
            self::error("Table have not a parameter");
        }
        return true;

    }

    private static function checkTypeFields($type, $value)
    {
        $allowBoolean = [true, false];
        $allowTypes = ['TINYINT','SMALLINT','MEDIUMINT','INT', 'BIGINT', 'FLOAT', 'DOUBLE', 'DATETIME', 'DATE', 'TIMESTAMP', 'CHAR', 'VARCHAR', 'BLOB', 'TEXT', 'ENUM'];
        $value = strtoupper($value);
        switch ($type) {
            case 'name':
                return (self::checkName($value)) ? true : false;
            break;
            case 'type':
                return (in_array($value, $allowTypes)) ? true : false;
            break;
            case 'increment':
                return (in_array($value, $allowBoolean)) ? true : false;
            break;
            case 'unsigned':
                return (in_array($value, $allowBoolean)) ? true : false;
            break;
            case 'nullabel':
                return (in_array($value, $allowBoolean)) ? true : false;
            break;
            case 'length':
                return ((int)$value > 0) ? true : false;
            break;
            case 'primary_key':
                return (in_array($value, $allowBoolean)) ? true : false;
            break;
            case 'default':
                return true;
            break;
        }
    }

    private static function checkName($name)
    {
        if ($name) {
            if (preg_match('/\s/',$name) > 0) {
                self::error("The table name must not be with whitespace");
            }
        } else {
            self::error("The name is not found");
        }
        return true;
    }

    private function getCommands()
    {
        return $this->operation;
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

    public static function error($error)
    {
        echo "\n" . $error . "\n";
        die;
    }
}