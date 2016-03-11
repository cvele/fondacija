<?php

namespace AppBundle\Annotation\Driver;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use AppBundle\Annotation\REST;

class RestAnnotationDriver {

    private $reader;

    public function __construct($reader)
    {
        $this->reader = $reader;
    }


    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
        }

        $object = new \ReflectionObject($controller[0]);

        foreach ($this->reader->getClassAnnotations($object) as $configuration) {
            if($configuration instanceof REST) {
                if (!isset($configuration->manager)) {
                    throw new \Exception("Entity manager service must be set in @REST annotation");
                }

                $manager = $controller[0]->get($configuration->manager);
                $controller[0]->manager = $manager;
             }
         }
    }
}
