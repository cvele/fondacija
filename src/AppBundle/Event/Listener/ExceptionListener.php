<?php

namespace AppBundle\Event\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Request\Exception\PayloadValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof PayloadValidationException) {
            $response = new JsonResponse($exception->getErrors(), JsonResponse::HTTP_BAD_REQUEST);
            $event->setResponse($response);
            return;
        } else if ($exception instanceof HttpException) {
            $response = new JsonResponse(['message'=>$exception->getMessage()], $exception->getStatusCode());
            $event->setResponse($response);
            return;
        }
    }
}
