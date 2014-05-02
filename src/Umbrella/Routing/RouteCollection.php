<?php

//---------------------------------------------------------------------------
// Route Collection
//---------------------------------------------------------------------------
//
// This class is the manager for all of the routes. It is in charge of
// creating route objects and running the route that is requested by
// the user.
//


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
     * Paths of the application
     *
     * @var array
     */
    private $paths = array();

    /**
     * Construct the RouteCollection
     *
     * @param  file $routes
     * @return \Umbrella\Routing\RouteCollection
     */
    public function __construct($routes, $paths)
    {
        $this->parser = new Parser();

        $this->paths = $paths;
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
    public function getRoute($path = "", $name = "")
    {
        for ($i = 0; $i < count($this->routes); $i++)
        {
            $checkRoute = $this->routes[$i];

            if($checkRoute->getName() === $name || $checkRoute->getPath() === $path)
            {
                return $checkRoute;
            }
            else if($checkRoute->getRegex() != null)
            {
                $regex = $checkRoute->getRegex();

                if(preg_match($regex, $path))
                {
                    preg_match_all('/\/(\w+)/', '/'.ltrim($path, $checkRoute->getStaticPath()), $matches, PREG_SET_ORDER);
                    foreach($matches as $match)
                    {
                        $values[] = $match[1];
                    }
                    $checkRoute->setValues($values);

                    return $checkRoute;
                }
            }
        }

        return false;
    }

    /**
     * Gets the path to the routes controller
     *
     * @param  \Umbrella\Routing\Route $route
     * @return mixed
     */
    public function getControllerPath(Route $route)
    {
        $startDir = $this->paths['src'] . '/Controllers/';

        if($route->getControllerPath() != null)
        {
            $fullPath = $startDir . $route->getControllerPath() . '/' . $route->getController();
        }
        else
        {
            $fullPath = $startDir . $route->getController();
        }

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
     * @TODO - add params to initialization if needed
     *
     * @param  string $controller
     * @param  string $path
     * @return Object
     */
    public function initController($controller, $path)
    {
        if($path)
        {
            $controller = 'Project\\Controllers\\' . $path . '\\' . $controller;
        }
        else
        {
            $controller = 'Project\\Controllers\\' . $controller;
        }

        return new $controller();
    }

    /**
     * Runs a given controller
     *
     * @TODO - add params to action if needed
     *
     * @param  Object $controller
     * @param  string $action
     * @param  array  $values
     * @return void
     */
    public function runController($controller = null, $action = null, $values = array())
    {
        if($action != null && method_exists($controller, $action))
        {
            if($values != null)
            {
                call_user_func_array(array($controller, $action), $values);
            }
            else
            {
                $controller->{$action}();
            }
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
            $controllerPath = $this->getControllerPath($route);

            if($controllerPath)
            {
                require $controllerPath;

                $controller = $this->initController($route->getControllerName(), $route->getControllerPath());
                $this->runController($controller, $route->getAction(), $route->getValues());
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
