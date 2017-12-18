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
            self::$tableQuery .= "CREATE TABLE $name (";
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
            $string = " $name INT($lenght)";
            if ($unsigned) {
                $string .= " UNSIGNED";
            }
            if ($increment) {
                $string .= " AUTO_INCREMENT";
            }
            if ($primary) {
                $string .= " PRIMARY KEY";
            }

            self::$tableQuery .= $string.",";
            return new self();
        } else {
            return false;
        }
    }

    public function string($name, $params = [])
    {
        if (self::checkName($name)) {
            $data = self::retrunParams($params);
            $lenght = $data['lenght'];
            $null = $data['null'];
            $default = $data['default'];
            $string = " $name VARCHAR($lenght)";
            if ($null) {
                $string .= ' NULL';
            } else {
                $string .= ' NOT NULL';
            }
            if ($default) {
                $string .= " DEFAULT '$default'";
            }
            self::$tableQuery .= $string;
            return new self();
        } else {
            return false;
        }
    }

    public function text($name, $params)
    {
        if (self::checkName($name) && count($params) > 0) {
            $data = self::retrunParams($params, true);
            $null = $data['null'];
            $default = $data['default'];
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

    private static function retrunParams($params, $text = false)
    {
        $data['lenght'] = (!$text) ? 191 : 0;
        $data['null'] = false;
        $data['default'] = false;
        if (!$text && isset($params['lenght']) && (int)$params['lenght'] > 0) {
            $data['lenght'] = (int)$params['lenght'];
        }
        if (isset($params['null']) && (bool)$params['null'] === true) {
            $data['null'] = true;
        }
        if (isset($params['default']) && (string)$params['default']) {
            $data['default'] = (string)$params['default'];
        }
        return $data;
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