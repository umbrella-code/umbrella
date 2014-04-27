<?php

//---------------------------------------------------------------------------
// Umbrella Application 
//---------------------------------------------------------------------------
//
// The Application class is the main object for the Umbrella Framework. It
// configures and runs the entire framework. All requests come here first
// and the Application delegates where they need to go.
//

namespace Umbrella\Foundation;

use PDO;
use Exception;
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
     * PDO instance representing the database connection
     *
     * @var PDO instance
     */
    private $pdo;

    /**
     * Construct an instance of the Application
     *
     * @param  array $paths
     * @param  array $db
     * @return void
     */
    public function __construct($paths, $db)
    {
        $this->parser = new Parser();
        $this->paths = $this->bindPaths($paths);
        $this->db = $this->connectDb($db);

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
            throw new Exception("Error parsing URI please check your URI.", 1);   
        }
    }

    /**
     * Create a new database connection
     *
     * @param  array $db
     * @return PDO Instance $pdo
     */
    public function connectDb(array $db)
    {
        $db   = $db['types'];
        $dbn  = $db['mysql']['driver'] . ':host=' . $db['mysql']['host'] . ';dbname=' . $db['mysql']['database'];
        $user = $db['mysql']['username'];
        $pass = $db['mysql']['password'];

        try
        {
            $pdo = new PDO($dbn, $user, $pass);
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }

        return $pdo;
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
