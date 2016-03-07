<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/api/v1/organizations")
 */
class OrganizationApiController extends RestController
{

    public function scope()
    {
      return 'organization';
    }

    /**
     * @see RestController::getRepository()
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->get('app.manager.organization')->getRepo();
    }

    /**
     * @see RestController::getNewEntity()
     * @return Object
     */
    public function getNewEntity()
    {
        return $this->get('app.manager.organization')->createClass();
    }
}
