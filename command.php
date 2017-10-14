<?php
define('ROOT', dirname(__FILE__));
require_once(ROOT.'/Bootstrap/App.php');

$new = new App;
$new->start($argv);

