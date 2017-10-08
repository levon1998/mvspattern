<?php

class Router
{
    private $routes;

    public function __construct()
    {
        $routesArray = ROOT.'/config/routes.php';
        $this->routes = include($routesArray);
    }

    private function getUrl()
    {
        if ($_SERVER['REQUEST_URI']) {
            return trim($_SERVER['REQUEST_URI'],'/');
        }
    }

    public function run()
    {
        //Get url request
        $url = $this->getUrl();

        //check in routes
        $routes = $this->routes;
        foreach ($routes as $name => $path) {
            if (preg_match("~$name~", $url)) {

                $internalRout = preg_replace("~$name~", $path, $url);
                $segment = explode('/', $internalRout);

                $controllerName = ucfirst(array_shift($segment).'Controller');
                $actionName = array_shift($segment);
                $controllerFile = ROOT.'/Controllers/'.$controllerName.'.php';
                $parameters = $segment;

                if (file_exists($controllerFile)) {
                    include_once($controllerFile);
                }

                $controller = new $controllerName;
                $result = call_user_func_array([$controller, $actionName],$parameters);

                if (!$result) {
                    break;
                }
            }
        }

    }
}