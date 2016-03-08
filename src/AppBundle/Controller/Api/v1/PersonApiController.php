<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/api/v1/persons")
 */
class PersonApiController extends RestController
{

    protected function scope()
    {
      return 'person';
    }

    /**
     * @see RestController::getRepository()
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->get('app.manager.person')->getRepo();
    }

    /**
     * @see RestController::getNewEntity()
     * @return Object
     */
    protected function getNewEntity()
    {
        return $this->get('app.manager.person')->createClass();
    }
}
