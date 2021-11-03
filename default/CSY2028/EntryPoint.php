<?php

namespace Core;

class EntryPoint {
    private $routes;

    public function __construct(\Jobs\Routes $routes)
    {
        $this->routes = $routes;
    }

    public function run()
    {
        $route = ltrim(explode('?', $_SERVER['REQUEST_URI'])[0], '/');

        $routes = $this->routes->getRoutes($route);

        $authentication = $this->routes->getAuthentication();

        $method = $_SERVER['REQUEST_METHOD'];

        //If page is not found. Return error page and stop this ->run function
        if(!isset($routes[$route]) || $routes[$route] == '') {
            $title = "Jo's Jobs - Page Not Found";
            $output = $this->loadTemplate('404.html.php');
            echo $this->loadTemplate('../templates/' . 'layout.html.php',
                $this->routes->getlayoutVariables($title , $output, null));
            return;
        }


        if (isset($routes[$route]['login']) && !$authentication->isLoggedIn()) {
            header('location: /login');
        } else if (isset($routes[$route]['permissions']) && !$this->routes->checkPermission($routes[$route]['permissions'])) {
            header('location: /permissionserror');
        } else {

            $controller = $routes[$route][$method]['controller'];
            $functionName = $routes[$route][$method]['function'];

            $page = $controller->$functionName();

            $title = $page['title'];


            if (isset($page['variables'])) {
                $output = $this->loadTemplate($page['template'], $page['variables']);
            } else {
                $output = $this->loadTemplate($page['template']);
            }
            echo $this->loadTemplate('../templates/' . 'layout.html.php',
                $this->routes->getlayoutVariables($title, $output, $page['mainClass'] ?? null));
        }

    }

    private function loadTemplate($filename, $variables = [])
    {
        if (isset($variables)) extract($variables);

        ob_start();
        include('../templates/'.$filename);

        return ob_get_clean();

    }

}