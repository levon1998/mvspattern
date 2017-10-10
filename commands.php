#!/usr/bin/php
<?php
define('ROOT', dirname(__FILE__));
require_once(ROOT.'/Bootstrap/App.php');

array_shift($argv);
if ($argv && isset($argv[0]) && count($argv) > 0) {
    if (isset($argv[1]) && $argv[0] == 'create:migration') {
        $getDate = date('d-m-Y H:i:s');
        $replaceDate = str_replace(['-',' ', ':'], '_', $getDate);
        $migrationName = $replaceDate.'_'.$argv[1];
        $html = App::migrationClass($argv[1]);
        echo $html;
        shell_exec("echo ".$html." >Database/Migrations/".$migrationName.".php");

    }
}

