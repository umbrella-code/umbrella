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
use Symfony\Component\Validator\Validation;
use Umbrella\Validation\Validator;

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
     * Instance of the Symfony Validator
     *
     * @var \Symfony\Component\Validator\LegacyValidator
     */
    protected $validator;

    /**
     * Contruct the controller
     */
    public function __construct()
    {
        $this->app = require $_SERVER['DOCUMENT_ROOT'].'/../app/config/bootstrap.php';
        $this->twig = $this->registerTwig();
        $this->validator = $this->createValidator();
    }

    /**
     * Register new Twig_Environment
     *
     * @return Twig_Environment $twig
     */
    public function registerTwig()
    {
        \Twig_Autoloader::register();

        $loader = new \Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'].'/../src/' . $this->app->getProjectName() . '/View');

        $twig = new \Twig_Environment($loader, array(
            'cache'       => $_SERVER['DOCUMENT_ROOT'].'/../app/cache/twig',
            'auto_reload' => true
        ));

        return $twig;
    }

    /**
     * Get EntityManager from $app
     *
     * @return \Umbrella\Foundation\Application->$em
     */
    public function getManager()
    {
        return $this->app->getEm();
    }

    /**
     * Creates an instance of the Symfony Validator
     *
     * @return \Symfony\Component\Validator $validator
     */
    public function createValidator()
    {
        $sv = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $validator = new Validator($sv);

        return $validator;
    }

    /**
     * Gets the Symfony Validator
     *
     * @return $this->validator
     */
    public function getValidator()
    {
        return $this->validator;
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
