<?php

namespace AppBundle\Annotation\Driver;

use Doctrine\Common\Annotations\Reader;//This thing read annotations
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;//Use essential kernel component
use AppBundle\Annotation\RequireTenant;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ControllerAnnotationDriver {

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
        $method = $object->getMethod($controller[1]);

        foreach ($this->reader->getMethodAnnotations($method) as $configuration) {
            if($configuration instanceof RequireTenant) {
                $tenantHelper = $controller[0]->get("multi_tenant.helper");
                $tenant = $tenantHelper->getCurrentTenant();

                if ($tenant === false || $tenant === null) {
                    throw new AccessDeniedHttpException("Client is missing tenant, required for this request.");
                }
             }
         }
    }
}
