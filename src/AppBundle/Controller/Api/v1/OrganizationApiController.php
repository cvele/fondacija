<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/api/v1/organizations")
 */
class OrganizationApiController extends RestController
{

    protected function scope()
    {
      return 'organization';
    }

    /**
     * @see RestController::getRepository()
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->get('app.manager.organization')->getRepo();
    }

    /**
     * @see RestController::getNewEntity()
     * @return Object
     */
    protected function getNewEntity()
    {
        return $this->get('app.manager.organization')->createClass();
    }
}
