<?php
require_once(ROOT.'/Components/Migration.php');
require_once(ROOT . '/Bootstrap/TableOrm.php');

class create_users_table extends Migration
{
    public function up()
    {
        return TableOrm::create('barev', [
            [
                'name' => 'id',
                'type' => 'int',
                'increment' => true,
                'unsigned' => true,
                'nullabel' => false,
                'primary_key' => true
            ],
            [
                'name' => 'haziv',
                'type' => 'varchar',
                'length' => 160,
                'nullabel' => true,
                'default' => 1
            ],
            [
                'name' => 'haziv2',
                'type' => 'varchar',
                'length' => '255',
                'nullabel' => false
            ]
        ]);
    }
    
    public function down()
    {
        
    }
}
