<?php

class App
{
    public $migrationTable = 'migrations';
    public $migrationDirectory = ROOT.'/Database/Migrations/';
    private $migrationPath = null;
    protected static $instance = null;

    public static function start($argv)
    {
        $argv = array_merge($argv, []);
        $actionList = ['create:migration'];

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
                self::getInstance()->help();
            break;
        }
    }

    protected static function migrationCreate($argv)
    {
        if (!isset($argv[1])) {
            self::getInstance()->error("Please Enter Migration Name");
        }
        self::checkMigrationName($argv[1]);
        $name = $argv[1];
        $content = strtr(self::getInstance()->getTemplate(), ['{className}' => $name]);
        $name= gmdate('ymd_His').'_'.$name;
        $pathInfo = self::getInstance()->migrationDirectory.$name.'.php';

        if (self::getInstance()->confirm("Create new migration '$name'?")) {
            file_put_contents($pathInfo,$content);
            echo "\n . New migration created successfully. \n";
        }

        echo "\n" . "Migration Is Created! " . "\n";
    }

    protected static function migrationMigrate($argv)
    {
        echo "migration";
    }

    protected static function migrationRefresh($argv)
    {
        echo "migration refresh";
    }

    protected static function help()
    {

    }

    private function getTemplate()
    {
        $classTemplate = <<<EOF
<?php

require_once(ROOT.'/Components/Migration.php');

class {className} extends Migration
{
    public function up()
    {
    
    }
    
    public function down()
    {
        
    }
}

EOF;
        return $classTemplate;
    }

    private function checkMigrationName($name)
    {
        if(!preg_match('/^\w+$/',$name)) {
            self::getInstance()->error("The name of the migration must contain letters, digits and/or underscore characters only");
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

    protected static function confirm($question)
    {
        echo "\n".$question." [yes,no] \n";
        return !strncasecmp(trim(fgets(STDIN)),'y',1);
    }
}