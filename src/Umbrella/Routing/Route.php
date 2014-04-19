<?php

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
        $this->controller = $route['controller'];
        $this->action = $route['action'];
    }
}