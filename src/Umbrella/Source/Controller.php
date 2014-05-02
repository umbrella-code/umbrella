<?php

//---------------------------------------------------------------------------
// Conroller
//---------------------------------------------------------------------------
//
// This is the main controller class that all user generated contollers need
// to extend. This will give a lot of functionality to each controller right
// out of the box.
//

namespace Umbrella\Source;

use Twig_;

class Controller
{
    /**
     * Twig instance to render view
     */
    protected $twig;

    /**
     * Umbrella Application from Bootstrap
     *
     * @var \Umbrella\Founation\Application
     */
    protected $app;

    /**
     * Contruct the controller
     */
    public function __construct()
    {
        $this->twig = $this->registerTwig();
        $this->app = require $_SERVER['DOCUMENT_ROOT'].'/../app/config/bootstrap.php';
    }

    /**
     * Register new Twig_Environment
     *
     * @return Twig_Environment $twig
     */
    public function registerTwig()
    {
        \Twig_Autoloader::register();

        $loader = new \Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'].'/../src/Project/Views');

        $twig = new \Twig_Environment($loader, array(
            'cache'       => $_SERVER['DOCUMENT_ROOT'].'/../app/cache/twig',
            'auto_reload' => true
        ));

        return $twig;
    }

    /**
     * Get EntityManager from $app
     *
     * @return \Umbrella\Foundation\Application:$em
     */
    public function getManager()
    {
        return $this->app->getEm();
    }

    /**
     * Render the requested view
     *
     * @param  string $view
     * @param  array  $data
     * @return void
     */
    public function render($view, array $data = array())
    {
         echo $this->twig->render($view, $data);
    }
}
