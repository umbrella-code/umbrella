<?php

namespace Umbrella\Exception;

use Exception;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class ExceptionHandler
{
    /**
     * Instance of the Whoops! exception handler
     *
     * @var \Whoops\Run
     */
    private $whoops;

    public function __construct(Run $whoops = null)
    {
        $this->whoops = $whoops;
    }

    /**
     * Register the Whoops! Exception Handler
     *
     * @return \Whoops\Run $whoops
     */
    public function start()
    {
        $whoops  = new Run();
        $handler = new PrettyPageHandler();
        $handler->setResourcesPath(__DIR__.'/resources');
        $handler->setEditor('sublime');

        $whoops->pushHandler($handler);
        $whoops->register();

        $this->whoops = $whoops;
    }
}
