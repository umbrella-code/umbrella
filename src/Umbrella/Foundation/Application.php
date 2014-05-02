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
use Doctrine\Common\ClassLoader;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
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
     * Parameters for database connection
     *
     * @var array
     */
    private $params;

    /**
     * Base Doctrine EntityManager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Construct an instance of the Application
     *
     * @param  array $paths
     * @param  array $db
     * @return void
     */
    public function __construct($paths, $params)
    {
        $this->parser = new Parser();
        $this->paths = $this->bindPaths($paths);
        $this->params = $this->addDbParams($params);
        $this->createClassLoaders();

        $this->em = $this->createBaseEm();
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
     * Create a new database connection
     *
     * @param  array $conn
     * @return array $conn
     */
    public function addDbParams(array $params)
    {
        $default = $params['default'];
        $types   = $params['types'];

        if(array_key_exists($default, $types))
        {
            $conn = $types[$default];
        }
        else
        {
            throw new Exception('Database type ' . $default . ' is not a valid type. Please check the value in the database.php file.', 1);
        }

        return $conn;
    }

    /**
     * Creates all necessary Doctrine ClassLoaders
     *
     * @return void
     */
    public function createClassLoaders()
    {
        $projLoader = new ClassLoader('Project', $this->paths['src']);
        $projLoader->register();
    }

    /**
     * Create the base Doctrine EntityManager
     *
     * @return \Doctrine\ORM\EntityManager $em;
     */
    public function createBaseEm()
    {
        $devEnv = true;
        $config = Setup::createAnnotationMetadataConfiguration(array($this->paths['src']."/Models"), $devEnv);
        $conn = $this->getParams();

        return $em = EntityManager::create($conn, $config);
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
     * Runs the application
     *
     * @return void
     */
    public function run()
    {
        $this->routeCollection->loadRoute($this->parseUri());
    }

    /**
     * Get $conn
     *
     * @return array $this->params
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get em
     *
     * @return $this->em
     */
    public function getEm()
    {
        return $this->em;
    }
}
