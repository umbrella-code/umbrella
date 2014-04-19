<?php

namespace Umbrella\Foundation;

use Symfony\Component\Yaml\Parser;
use Umbrella\Routing\RouteCollection;

class Application
{
    /**
     * Paths defined in the paths file
     *
     * @var array
     */
    private $paths = array();

    /**
     * RouteCollection from app
     *
     * @var \Umbrella\Routing\RouteCollection
     */
    private $routeCollection = array();

    /**
     * Yaml Parser
     *
     * @var \Symfony\Component\Yaml\Parser
     */
    private $parser = null;

    /**
     * Construct an instance of the Application
     *
     * @param  array $paths
     * @return void
     */
    public function __construct($paths)
    {
        $this->parser = new Parser();
        $this->paths = $this->bindPaths($paths);

        $this->routeCollection = $this->bindRouteCollection();
    }

    /**
     * Connect app paths to the Application
     *
     * @param  array $paths
     * @return void
     */
    public function bindPaths(array $paths)
    {
        foreach($paths as $key => $val)
        {
            $paths[$key] = realpath($val);
        }

        return $paths;
    }

    /**
     * Bind RouteCollection to app
     *
     * @return \Umbrella\Routing\RouteCollection $routeCollection
     */
    public function bindRouteCollection()
    {
        $routeCollection = new RouteCollection($this->paths['app'].'/routes.yml', $this->paths);

        return $routeCollection;
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
            throw new \Exception("Error parsing URI please check your URI.", 1);   
        }
    }

    /**
     * Runs the application
     *
     * @return void
     */
    public function run()
    {
        $this->routeCollection->loadRoute($this->parseUri());
    }
}
