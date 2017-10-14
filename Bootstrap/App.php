<?php
require_once(ROOT.'/Vendor/Templates/Template.php');
class App
{
    public $migrationTable = 'migrations';
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
        echo "migration";
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

    protected static function help()
    {


    }
}