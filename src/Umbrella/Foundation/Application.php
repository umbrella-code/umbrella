<?php

namespace Umbrella\Foundation;

use Symfony\

class Application
{
    /**
     * Controller that is called
     *
     * @var string
     */
    private $controller = null;

    /**
     * Method called within the controller
     *
     * @var string
     */
    private $action = null;

    /**
     * First parameter passed to the function (if exists)
     *
     * @var string
     */
    private $param1 = null;

    /**
     * Second parameter passed to the function (if exists)
     *
     * @var string
     */
    private $param2 = null;

    /**
     * Third parameter passed to the function (if exists)
     *
     * @var string
     */
    private $param3 = null;

    /**
     * Paths defined in the paths file
     *
     * @var array
     */
    private $paths = array();

    /**
     * Construct an instance of the Application
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
     * Gets the URL and splits it into usable parts
     *
     * @return string
     */
    public function splitUrl()
    {
        $url = (isset($_GET['url']) ? $_GET['url'] : null);

        if(isset($url))
        {
            $url = rtrim($url, '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);

            $this->controller   = (isset($url[0]) ? $url[0] : null);
            $this->action       = (isset($url[1]) ? $url[1] : null);
            $this->param1       = (isset($url[2]) ? $url[2] : null);
            $this->param2       = (isset($url[3]) ? $url[3] : null);
            $this->param3       = (isset($url[4]) ? $url[4] : null);
        }
        else
        {
            //throw new Exception("The URL was not found. Please check your sent URL.");
        }
    }

    /**
     * Loads the controller based one the URL
     *
     * @return void
     */
    public function loadController()
    {
        $this->splitUrl();

        $controllerPath = $this->paths['src'] . '\Controllers\\' . strtolower($this->controller) . 'Controller.php';
        if(file_exists($controllerPath))
        {
            require($controllerPath);
            $this->controller = new $this->controller();

            if(method_exists($this->controller, $this->action))
            {
                if(isset($this->param3))
                {
                    $this->controller->{$this->action}($this->param1, $this->param2, $this->param3);
                }
                else if(isset($this->param2))
                {
                    $this->controller->{$this->action}($this->param1, $this->param2);
                }
                else if(isset($this->param1))
                {
                    $this->controller->{$this->action}($this->param1);
                }
                else
                {
                    $this->controller->{$this->action}();
                }
            }
            else
            {
                //throw new Exception('Controller ' . $this->controller . ' does not have any method named ' . $this->action . '.');
            }
        }
        else
        {
            echo 'file does not exist';
        }
    }

    /**
     * Starts the application
     *
     * @return void
     */
    public function start()
    {
        $this->loadController();
    }
}