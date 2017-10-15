<?php

//Front Controller

//Connect Files
    define('ROOT', dirname(__FILE__));
    require_once(ROOT.'/Components/Router.php');
    $configs = require_once(ROOT.'/Conf.php');
    require_once (ROOT.'/Components/Configs.php');

//All Settings And Check Conf.php file constans
    $conf = new Configs();
    $conf->setConstants($configs);

//Mysql Connect
    $db = $conf->dbConfigs($configs);
//Router
    $router = new Router();
    $router->run();