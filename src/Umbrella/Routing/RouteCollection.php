<?php

namespace Umbrella\Routing;

use Symfony\Component\Yaml\Parser;
use Umbrella\Routing\Route;

class RouteCollection
{
    /**
     * All routes present
     *
     * @var array
     */
    private $routes = array();

    /**
     * Symfony parser to parse YAML
     *
     * @var \Symfony\Component\Yaml\Parser
     */
    private $parser;

    /**
     * Construct the RouteCollection
     *
     * @param  file $routes
     * @return \Umbrella\Routing\RouteCollection
     */
    public function __construct($routes)
    {
        $this->parser = new Parser();

        $this->routes = $this->buildRoutes($this->parseRoutes($routes));
    }

    /**
     * Parses routes from routes file
     *
     * @param  file $routes
     * @return void
     */
    public function parseRoutes($routes)
    {
        $routes = file_get_contents($routes);

        return $this->parser->parse($routes);
    }

    /**
     * Build Route object for each route
     *
     * @param  array $routes
     * @return array \Umbrella\Routing\Route
     */
    public function buildRoutes(array $routes)
    {
        foreach ($routes as $key => $val)
        {
            $routes[$key]['name'] = $key;
            $routeObj = new Route($routes[$key]);

            $newRouteObjs[] = $routeObj;
        }

        return $newRouteObjs;
    }

    /**
     * Search the application routes
     *
     * @param  string $path
     * @param  string $name
     * @return mixed
     */
    public function getRoute($path = "", $name = "") // fix this, not working because $this->routes is an array of objects
    {
        foreach($this->routes as $key => $val)
        {
            if($val->path === $path || $val->name === $name)
            {
                return $routes[$key];
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
     * @param  string $uri
     * @return void
     */
    public function loadRoute($uri)
    {
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
                throw new \Exception("404 controller not found.", 1);
            }
        }
        else
        {
            throw new \Exception("No route was found that matched the current URI.", 1);
        }
    }

    /**
     * Gets the array of routes
     *
     * @return  array
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}