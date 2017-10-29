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
        $migrations = self::getInstance()->getMigrations();
        $table = self::getInstance()->migrationTable;
        $total=count($migrations);
        if(count($total) > 0) {
            echo "\n Total $total new ".($total===1 ? 'migration':'migrations')." to be applied:\n";
        }
        foreach ($migrations as $migration) {
                echo $migration['name']."\n";
        }
        echo "\n";
        $i = 0;
        foreach($migrations as $migration) {
            if(self::getInstance()->migrate($migration['name'])===false) {
                $i++;
                echo "\nMigration failed. All later migrations are canceled.\n";
                return;
            }
        }
        if ($i == 0) {
            echo "Migrated up successfully.\n";
            self::getInstance()->db->insert($table, $migrations);
        }
    }

    protected static function migrate($class)
    {
        $migration = self::getInstance()->getMigrationClass($class);
        if($migration->up()) {
            self::getInstance()->db->queryToDb($migration->up());
        } else {
            return false;
        }
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

    protected static function getMigrationClass($name)
    {
        $file = self::getInstance()->migrationDirectory.$name;
        require_once($file);
        $name = str_replace('.php', '',substr($name, 14));
        $migration = new $name;
        return $migration;
    }

    protected static function getMigrations()
    {
        $rowsMigration = self::getInstance()->getMigrationHistory();
        $migrations = [];
        if (is_dir(self::getInstance()->migrationDirectory)) {
            $files = opendir(self::getInstance()->migrationDirectory);
            while (($file = readdir($files)) !== false) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (!isset($rowsMigration[$file])) {
                    $migrations[] = [
                        'name' => $file,
                        'active' => 1
                    ];
                }
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
        $rows = self::getInstance()->db->findAll(self::getInstance()->migrationTable);
        $migrations = [];
        foreach ($rows as $row) {
            $migrations[$row['name']] = $row['id'];
        }
        return $migrations;
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