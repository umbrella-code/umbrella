<?php

//---------------------------------------------------------------------------
// Route
//---------------------------------------------------------------------------
//
// The is the object for which all route objects are created. The information
// in the routes.yml file is sent here by the RouteCollection and saved as a
// a new Route.
//

namespace Umbrella\Routing;

class Route
{
    /**
     * Name of route
     *
     * @var string
     */
    private $name = "";

    /**
     * Path of route
     *
     * @var string
     */
    private $path = "";

    /**
     * Static portion of the path
     *
     * @var string
     */
    private $static_path = "";

    /**
     * Regex of path
     *
     * @var string
     */
    private $regex = "";

    /**
     * Parameters for the route
     *
     * @var array
     */
    private $params = array();

    /**
     * Values of the parameters
     *
     * @var array
     */
    private $values = array();

    /**
     * Controller that is called
     *
     * @var \Umbrella\Source\Controller
     */
    private $controller = null;

    /**
     * Name of the called controller
     *
     * @var string
     */
    private $controller_name = "";

    /**
     * Parent directories of controller
     *
     * @var string
     */
    private $controller_path = "";

    /**
     * Method called within the controller
     *
     * @var string
     */
    private $action = null;

    /**
     * Constructs a new route abject
     *
     * @param  array $route
     * @return \Umbrella\Routing\Route $route
     */
    public function __construct(array $route)
    {
        $this->name = $route['name'];
        $this->path = $this->compilePath($route['path']);

        $this->parseControllerString($route['controller']);
    }

    /**
     * Gets parts of route controller string
     *
     * @param  string $controllerString
     * @return void
     */
    public function parseControllerString($controllerString)
    {
        $partsArray = explode('@', $controllerString);

        if(count($partsArray) > 2)
        {
            throw new \Exception("Error parsing controller string, too many @'s found. Only use one to destinguish the controller method.", 1);
        }
        else
        {
            if(strpos($partsArray[0], ':'))
            {
                $parts = explode(':', $partsArray[0]);

                $this->controller_name = array_pop($parts);
                $this->controller = $this->controller_name . '.php';
                $this->controller_path = implode('/', $parts);
            }
            else
            {
                $this->controller_name = $partsArray[0];
                $this->controller = $this->controller_name . '.php';
            }
            
            $this->action = $partsArray[1];
        }
    }

    /**
     * Compiles route path
     *
     * @param string $path
     */
    public function compilePath($path)
    {
        if(preg_match_all('/\{(\w+)\}/', $path, $matches, PREG_SET_ORDER) == false)
        {
            return $path;
        }
        else
        {
            foreach($matches as $match)
            {
                $this->params[] = $match[1];
            }

            $static = preg_replace('/\{(\w+)\}/i', "", $path);
            $static = rtrim($static, '/');

            $path = '/' . str_replace('/', '\/', $path) . '/';
            $regex = preg_replace('/\{(\w+)\}/', '(\w+)', $path);

            $this->static_path = $static;
            $this->regex = $regex;
        }
        
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return \Umbrella\Routing\Route $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path
     *
     * @param  string $path
     * @return \Umbrella\Routing\Route $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get static_path
     *
     * @return string
     */
    public function getStaticPath()
    {
        return $this->static_path;
    }

    /**
     * Set static_path
     *
     * @param  string $staticPath
     * @return \Umbrella\Routing\Route $this
     */
    public function setStaticPath($staticPath)
    {
        $this->static_path = $staticPath;

        return $this;
    }

    /**
     * Get controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set controller
     *
     * @param  string $controller
     * @return \Umbrella\Routing\Route $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Get controller_name (no .php)
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->controller_name;
    }

    /**
     * Set controller_name
     *
     * @param  string $controllerName
     * @return \Umbrella\Routing\Route $this
     */
    public function setControllerName($controllerName)
    {
        $this->controller_name = $controllerName;

        return $this;
    }

    /**
     * Set conroller_path
     *
     * @param  string $controllerPath
     * @return \Umbrella\Routing\Route $this
     */
    public function setControllerPath($controllerPath)
    {
        $this->controller_path = $controllerPath;

        return $this;
    }

    /**
     * Get controller_path
     *
     * @return string
     */
    public function getControllerPath()
    {
        return $this->controller_path;
    }

    /**
     * Get regex
     *
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Set regex
     *
     * @param  string $regex
     * @return \Umbrella\Routing\Route $this
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;

        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set params
     *
     * @param  array $params
     * @return \Umbrella\Routing\Route $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set values
     *
     * @param  array $values
     * @return \Umbrella\Routing\Route $this
     */
    public function setValues(array $values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set action
     *
     * @param  string $action
     * @return \Umbrella\Routing\Route $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }
}