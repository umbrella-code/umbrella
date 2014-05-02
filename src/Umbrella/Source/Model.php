<?php

//---------------------------------------------------------------------------
// Model
//---------------------------------------------------------------------------
//
// This is the main model for the application. This will give functionalities
// like queries to all created models. All other models that are created
// must extend this class.
//

namespace Umbrella\Source;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ApcCache;

class Model
{
    /**
     * Instance of the Doctrine Common AnnotationReader
     *
     * @var \Doctrine\Common\Annotations\AnnotationReader $reader
     */
    protected $reader;

    public function __construct()
    {
        $this->reader = $this->createAnnotationReader();
    }

    /**
     * Creates the Doctrine Common AnnotationReader
     *
     * @return \Doctrine\Common\Annotations\AnnotationReader $reader
     */
    public function createAnnotationReader()
    {
        AnnotationRegistry::registerFile($_SERVER['DOCUMENT_ROOT'].'/../vendor/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');
        $reader = new CachedReader(new AnnotationReader(), $_SERVER['DOCUMENT_ROOT'].'/../app/cache/doctrine/annotations', $debug = true);

        return $reader;
    }
}
