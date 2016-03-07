<?php

namespace AppBundle\Event\Listener;

use AppBundle\Controller\Api\v1\RestController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpMethodListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof RestController) {
            if (!in_array($event->getRequest()->getMethod(), $controller[0]->implementsMethods())) {
                throw new HttpException(405,
                  sprintf('The %s method is not supported. Supported methods are %s',
                    $event->getRequest()->getMethod(),
                    implode(", ", $controller[0]->implementsMethods())
                    )
                );
            }
        }
    }
}
