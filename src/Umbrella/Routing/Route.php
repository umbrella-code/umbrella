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
     * Parameters for the route
     *
     * @var array
     */
    private $params = array();

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
        $this->path = $route['path'];

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
     * @return \Umbrella\Routing\Route
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
     * @return \Umbrella\Routing\Route
     */
    public function setPath($path)
    {
        $this->path = $path;

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
     * @return \Umbrella\Routing\Route
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
     * @return \Umbrella\Routing\Route
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
     * @return \Umbrella\Routing\Route
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
     * @return \Umbrella\Routing\Route
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }
}