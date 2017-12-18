<?php
require_once(ROOT.'/Components/Migration.php');
require_once(ROOT . '/Bootstrap/TableOrm.php');

class create_users_table extends Migration
{
    public function up()
    {
        return TableOrm::create('barev')
            ->increment("id")
            ->string("name", true)
            ->make();
    }
    
    public function down()
    {
        
    }
}
