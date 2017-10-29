<?php

class TableOrm
{
    public $table;
    public $db;
    protected $columns = [];
    protected $operation = [];

    public function __construct($table)
    {
        $this->table = $table;
    }

    public static function create($name, $params)
    {
        if (self::checkName($name)) {
            return self::checkColumns($name, $params);
        } else {
            return false;
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

    private function getColumns($name, $params)
    {
        $i = 0;
        $sql = "CREATE TABLE $name (";
        foreach ($params as $param) {
            self::validationForFields($param);
            if (isset($param['name']) && $param['name']) {
                $sql .= ($i == 0) ? '' : ', ';
                $sql .= $param['name'];
            }
            if (isset($param['type']) && $param['type']) {
                $sql .= ' '. $param['type'];
                if (isset($param['length']) && (int)$param['length'] > 0) {
                    $sql .= "(".$param['length'].")";
                } else {
                    $sql .= "(11)";
                }
            }
            if (isset($param['increment']) && $param['increment']) {
                if (isset($param['unsigned']) && (bool)$param['unsigned']) {
                    $sql .= ' UNSIGNED AUTO_INCREMENT';
                } else {
                    $sql .= ' AUTO_INCREMENT';
                }
            }
            if (isset($param['nullabel'])) {
                if ($param['nullabel']) {
                    $sql .= ' NULL';
                } else {
                    $sql .= ' NOT NULL';
                }
            }
            if (isset($param['primary_key']) && $param['primary_key']) {
                $sql .= ' PRIMARY KEY';
            }
            if (isset($param['default']) && $param['default']) {
                $sql .= ' DEFAULT '. $param['default'];
            }
            $i++;
        }
        $sql .= ");";
        return $sql;
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

    public static function error($error)
    {
        echo "\n" . $error . "\n";
        die;
    }
}