<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/api/v1/persons")
 */
class PersonApiController extends RestController
{

    public function scope()
    {
      return 'person';
    }

    /**
     * @see RestController::getRepository()
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->get('app.manager.person')->getRepo();
    }

    /**
     * @see RestController::getNewEntity()
     * @return Object
     */
    public function getNewEntity()
    {
        return $this->get('app.manager.person')->createClass();
    }
}
