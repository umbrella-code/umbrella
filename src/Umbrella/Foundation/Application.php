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

use Exception;
use Symfony\Component\Yaml\Parser;
use Doctrine\Common\ClassLoader;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use Umbrella\Routing\RouteCollection;
use Umbrella\Exception\ExceptionHandler;

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
     * All needed Doctrine ClassLoaders
     *
     * @var array \Doctrine\Common\ClassLoader
     */
    private $loaders;

    /**
     * Name of Project
     *
     * @var string
     */
    private $project_name = "";

    /**
     * Construct an instance of the Application
     *
     * @param  array $paths
     * @param  array $db
     * @return void
     */
    public function __construct($paths)
    {
        $this->parser = new Parser();
        $this->startHandlingExceptions();
        $this->paths           = $this->bindPaths($paths);
        $this->loaders         = $this->createClassLoaders();
        $this->configureApplication();
    }

    /**
     * Start handling exceptions
     *
     * @return void
     */
    public function startHandlingExceptions()
    {
        $handler = new ExceptionHandler();
        $handler->start();
    }

    /**
     * Configure the Umbrella Application
     *
     * @param  array $config
     * @return void
     */
    public function configureApplication()
    {
        $config = file_get_contents($this->paths['app'].'/config/config.yml');
        $config = $this->parser->parse($config);

        $this->project_name     = $config['application']['project_name'];
        $this->routeCollection  = $this->bindRouteCollection($this->project_name);

        $this->addDbParams($config['database']['default'], $config['database']['types']);
        $this->createBaseEm($config['application']['environment']);
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
    public function addDbParams($default = "", array $types)
    {
        if(array_key_exists($default, $types))
        {
            $conn = $types[$default];
        }
        else
        {
            throw new Exception('Database type ' . $default . ' is not a valid type. Please check the value in the database.php file.', 1);
        }

        $this->params = $conn;
    }

    /**
     * Creates all necessary Doctrine ClassLoaders
     *
     * @return void
     */
    public function createClassLoaders()
    {
        //$projLoader = new ClassLoader('Project', $this->paths['src']);
        //$projLoader->register();

        //return $projLoader;
    }

    /**
     * Create the base Doctrine EntityManager
     *
     * @return \Doctrine\ORM\EntityManager $em;
     */
    public function createBaseEm($env)
    {
        if($env == 'dev')
        {
            $devEnv = true;
        }
        else
        {
            $devEnv = false;
        }

        AnnotationRegistry::registerFile($this->paths['root'] . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');
        AnnotationRegistry::registerAutoloadNamespace('Symfony\Component\Validator\Constraints', $this->paths['root'] . '/vendor/symfony/validator');

        $reader = new FileCacheReader(new AnnotationReader(), $this->paths['app'] . '/cache/doctrine/annotations', $debug = true);

        $driverImpl = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($reader, array($this->paths['src']."/Model"));
        $config = Setup::createAnnotationMetadataConfiguration(array($this->paths['src']."/Model"), $devEnv);
        $config->setMetadataDriverImpl($driverImpl);
        $conn = $this->getParams();

        return $em = EntityManager::create($conn, $config);
    }

    /**
     * Bind RouteCollection to app
     *
     * @param  string $name
     * @return \Umbrella\Routing\RouteCollection $routeCollection
     */
    public function bindRouteCollection($name = "")
    {
        $routeCollection = new RouteCollection($this->paths['app'].'/routes.yml', $this->paths, $name, $this->parser);

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

    /**
     * Get project_name
     *
     * @return \Umbrella\Foundation\Application:$project_name
     */
    public function getProjectName()
    {
        return $this->project_name;
    }
}
