<?php

require_once(ROOT.'/Components/Migration.php');
require_once(ROOT . '/Bootstrap/TableOrm.php');

class users extends Migration
{
    public function up()
    {
        return TableOrm::create('users', [
            [
                'name' => 'id',
                'type' => 'int',
                'unsigned' => true,
                'increment' => true,
                'primary_key' => true
            ],
            [
                'name' => 'first_name',
                'nullabel' => false,
                'type' => 'varchar',
                'length' => 255
            ],
            [
                'name' => 'last_name',
                'nullabel' => true,
                'type' => 'varchar',
                'length' => 255
            ],
            [
                'name' => 'active',
                'nullabel' => false,
                'type' => 'tinyint',
            ]
        ]);
    }
    
    public function down()
    {
        
    }
}
