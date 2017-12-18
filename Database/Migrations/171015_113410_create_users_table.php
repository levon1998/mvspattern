<?php
require_once(ROOT.'/Components/Migration.php');
require_once(ROOT . '/Bootstrap/TableOrm.php');

class create_users_table extends Migration
{
    public function up()
    {
        return TableOrm::create('barev')
            ->increment("id")
            ->string("name", ['length' => 191, 'null' => true, 'default' => 'llll'])
            ->text("description", ['null' => false])
            ->make();
    }
    
    public function down()
    {
        
    }
}
