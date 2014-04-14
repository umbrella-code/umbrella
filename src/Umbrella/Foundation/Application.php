<?php

namespace Umbrella\Foundation;

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
    private $param_one = null;

    /**
     * Second parameter passed to the function (if exists)
     *
     * @var string
     */
    private $param_two = null;

    /**
     * Third parameter passed to the function (if exists)
     *
     * @var string
     */
    private $param_three = null;

    /**
     * Database object from the parameters file
     *
     * @var Database Object
     */
    private $db = null;

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
        foreach($path as $key => $value)
        {
            $this->paths["app.{$key}", realpath($value)];
        }
    }

    /**
     * Open a new database connection
     *
     * @param  array $params
     * @return void
     */
    public function connectDb(array $params)
    {

    }
}