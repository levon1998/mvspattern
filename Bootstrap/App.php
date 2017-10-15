<?php
require_once(ROOT.'/Vendor/Templates/Template.php');
require_once(ROOT.'/Bootstrap/Query.php');
class App
{
    public $migrationTable = 'migrations';
    private $db = null;
    private $migrationDirectory = ROOT.'/Database/Migrations/';
    private $modelDirectory = ROOT.'/Models/';
    private $controllerDirectory = ROOT.'/Controllers/';
    protected static $instance = null;

    public static function start($argv)
    {
        $argv = array_merge($argv, []);
        $actionList = ['create:migration', 'help', 'migrate', 'migrate:refresh', 'create:model', 'create:controller'];

        if (isset($argv[1]) && $argv[1]) {
            if (!in_array($argv[1], $actionList)) {
                self::getInstance()->error("Not Current Command. Run the command.php help");
            }
        }
        if (isset($argv[0]) && $argv[0] == 'command.php') {
            unset($argv[0]);
        }
        $argv = array_merge($argv, []);
        $action = (isset($argv[0]) && $argv[0]) ? $argv[0] : 'migrate';

        switch ($action) {
            case 'migrate':
            default:
                self::getInstance()->migrationMigrate($argv);
            break;
            case 'migrate:refresh':
                self::getInstance()->migrationRefresh($argv);
            break;
            case 'create:migration':
                self::getInstance()->migrationCreate($argv);
            break;
            case 'help':
                self::getInstance()->getTemplate('Help');
            break;
            case 'create:model':
                self::getInstance()->createModel($argv);
            break;
            case 'create:controller':
                self::getInstance()->createController($argv);
            break;
        }
    }

    protected static function migrationCreate($argv)
    {
        self::getInstance()->checkCommandName($argv, "Migration");
        self::checkMigrationName($argv[1], "Migration");
        $name = $argv[1];
        $content = strtr(self::getInstance()->getTemplate("Migration"), ['{className}' => $name]);
        $name= gmdate('ymd_His').'_'.$name;
        $pathInfo = self::getInstance()->migrationDirectory.$name.'.php';

        if (self::getInstance()->confirm("Create new migration '$name'?")) {
            file_put_contents($pathInfo,$content);
            echo "\n . New Migration Created Successfully. \n";
        }
    }

    protected static function migrationMigrate($argv)
    {
        self::getInstance()->getMigrations();
    }

    protected static function migrationRefresh($argv)
    {
        echo "migration refresh";
    }

    protected static function createModel($argv)
    {
        self::getInstance()->checkCommandName($argv, "Model");
        self::checkMigrationName($argv[1], "Model");
        $name = $argv[1];
        $content = strtr(self::getInstance()->getTemplate('Model'), ['{className}' => $name]);
        $filePath = self::getInstance()->modelDirectory.$name.'.php';

        if (self::getInstance()->confirm("Create New Model '$name'?")) {
            file_put_contents($filePath, $content);
            echo "\n . New Model Created Successfully. \n";
        }
    }

    protected static function createController($argv)
    {
        self::getInstance()->checkCommandName($argv, "Controller");
        self::checkMigrationName($argv[1], "Controller");
        $name = ucfirst($argv[1]).'Controller';
        $content = strtr(self::getInstance()->getTemplate('Controller'), ['{className}' => $name]);
        $filePath = self::getInstance()->controllerDirectory.$name.'.php';
        if (self::getInstance()->confirm("Create New Controller '$name'?")) {
            file_put_contents($filePath,$content);
            echo "\n . New Controller Created Successfully. \n";
        }
    }

    public static function getInstance()
    {
        if(self::$instance !== null) {
            return self::$instance;
        }
        self::$instance = new self;
        return self::$instance;
    }

    protected static function error($error)
    {
        echo "\n" . $error . "\n";
        die;
    }

    private function checkMigrationName($name, $type)
    {
        if(!preg_match('/^\w+$/',$name)) {
            self::getInstance()->error("The Name Of The ".$type." Must Contain Letters, Digits And/Or Underscore Characters Only");
        }
    }

    private function checkCommandName($argv, $type)
    {
        if (!isset($argv[1])) {
            self::getInstance()->error("Please Enter ".$type." Name");
        }
    }

    protected static function confirm($question)
    {
        echo "\n".$question." [yes,no] \n";
        return !strncasecmp(trim(fgets(STDIN)),'y',1);
    }

    private function getTemplate($type)
    {
        return Template::start($type);
    }

    protected static function getMigrations()
    {
        self::getInstance()->getMigrationHistory();
        $migrations = [];
        if (is_dir(self::getInstance()->migrationDirectory)) {
            $files = opendir(self::getInstance()->migrationDirectory);
            while (($file = readdir($files)) !== false) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                $migrations[] = $file;
//                $path=self::getInstance()->migrationDirectory.$file;
//                if(preg_match('/^(m(\d{6}_\d{6})_.*?)\.php$/',$file,$matches) && is_file($path) && !isset($applied[$matches[2]]))  {
//                    $migrations[] = $matches[1];
//                }
            }
            closedir($files);
        }
        sort($migrations);
        return $migrations;

    }

    protected static function getMigrationHistory()
    {
        self::getInstance()->db = new Query();
        if (!self::getInstance()->db->getTable(self::getInstance()->migrationTable)) {
            self::getInstance()->createHistoryTable();
        }

    }

    protected static function createHistoryTable()
    {
        echo "\n".'Creating migration history table "'.self::getInstance()->migrationTable.'"...'. "\n";
        $sql = "CREATE TABLE IF NOT EXISTS `".self::getInstance()->migrationTable."`(
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` varchar(190) NOT NULL,
            `active` int DEFAULT 1
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $result = self::getInstance()->db->queryToDb($sql);
    }
}