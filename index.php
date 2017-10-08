<?php

//Front Controller

//All Settings
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

//Connect Files
    define('ROOT', dirname(__FILE__));
    require_once(ROOT.'/Components/Router.php');

//Mysql Connect


//Router
    $router = new Router();
    $router->run();