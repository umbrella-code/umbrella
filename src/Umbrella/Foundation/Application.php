<?php

namespace Umbrella\Foundation;

class Application
{
    /**
     * Controller that is called
     *
     * @var Object
     */
    private $controller = null;

    /**
     * Name of the called controller
     *
     * @var string
     */
    private $controller_name = "";

    /**
     * Method called within the controller
     *
     * @var string
     */
    private $action = null;

    /**
     * Paths defined in the paths file
     *
     * @var array
     */
    private $paths = array();

    /**
     * All application routes
     *
     * @var array
     */
    private $routes = array();

    /**
     * Construct an instance of the Application
     *
     * @return \Umbrella\Foundation\Application
     */
    public function __construct()
    {
    }

    /**
     * Connect app paths to the Application
     *
     * @param  array $paths
     * @return void
     */
    public function bindPaths(array $paths)
    {
        foreach($paths as $key => $value)
        {
            $this->paths[$key] = realpath($value);
        }
    }

    /**
     * Binds the routes to the app
     *
     * @param  array $routes
     * @return void
     */
    public function bindRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Parse the URL
     *
     * @return string
     */
    public function parseUri()
    {
        $uri = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null);

        if(isset($uri))
        {
            $uri = '/' . ltrim(trim($uri), '/');
            $uri = filter_var($uri, FILTER_SANITIZE_URL);

            return $uri;
        }
        else
        {
            echo 'Error parsing URI.';
        }
    }

    /**
     * Search the application routes
     *
     * @param  string $path
     * @param  string $name
     * @return mixed
     */
    public function getRoute($path = "", $name = "")
    {
        foreach($this->routes as $key => $val)
        {
            if($val['path'] === $path || $val['name'] === $name)
            {
                return $this->routes[$key];
            }
        }
        return false;
    }

    /**
     * Gets the path to the routes controller
     *
     * @param  string $controller
     * @return mixed
     */
    public function getControllerPath($controller)
    {
        $fullPath = $this->paths['src'] . '/Controllers/' . $controller;

        if(file_exists($fullPath))
        {
            return $fullPath; 
        }
        else
        {
            return false;
        }
    }

    /**
     * Initializes a new instance of a controller
     *
     * @param  string $controller_name
     * @return Object
     */
    public function initController($controller)
    {
        return new $controller();
    }

    /**
     * Runs a given controller
     *
     * @param  Object $controller
     * @param  string $action
     * @return void
     */
    public function runController($controller = null, $action = null)
    {
        if($action != null && method_exists($controller, $action))
        {
            $controller->{$action}();
        }
        else
        {
            $controller;
        }
    }

    /**
     * Loads the route based one the URL
     *
     * @return void
     */
    public function loadRoute()
    {
        $uri = $this->parseUri();
        $route = $this->getRoute($uri);

        if($route)
        {
            $this->controller_name  = $route['controller'];
            $this->action           = $route['action'];

            $controllerPath = $this->getControllerPath($this->controller_name . '.php');

            if($controllerPath)
            {
                require $controllerPath;
                
                $this->controller = $this->initController($this->controller_name);
                $this->runController($this->controller, $this->action);
            }
            else
            {
                echo '404 File Not Found.';
            }
        }
        else
        {
            echo 'No route found.';
        }
    }

    /**
     * Runs the application
     *
     * @return void
     */
    public function run()
    {
        $this->loadRoute();
    }
}
