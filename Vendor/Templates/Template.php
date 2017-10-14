<?php

class Template
{
    public static function start($type)
    {
        switch($type){
            case 'Migration':
                return self::getMigrationTemplate();
            break;
            case 'Help':
                return self::getHelpTemplate();
            break;
            case 'Model':
                return self::getModelTemplate();
            break;
            case 'Controller':
                return self::getControllerTemplate();
            break;
        }

    }

    protected static function getMigrationTemplate()
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

    protected static function getHelpTemplate()
    {
        echo <<<EOF
        
USAGE
    php command.php [action] [parameter]
	  
DESCRIPTION
    This command provides support for database migrations.
    
EXAMPLES
    * php command.php migrate
      Migrate all tables
    * php command.php create:migrate [name]
      Create new migration file (class)
    * php command.php migrate:refresh
      Refresh all tables and clear datas
    * php command.php help
      Get all commands 
    * php command.php create:model [name]
      Create new model file (class)
    * php command.php create:controller [name]
      Create new controller file (Controller)
      
EOF;
    }

    protected static function getModelTemplate()
    {
        $classTemplateModel = <<<EOF
<?php

require_once(ROOT.'/Models/Models.php');

class {className} extends Models
{
    
}

EOF;

    return $classTemplateModel;
    }

    protected static function getControllerTemplate()
    {
        $classTemplate = <<<EOF
<?php

class {className}
{
    
}

EOF;
        return $classTemplate;
    }
}