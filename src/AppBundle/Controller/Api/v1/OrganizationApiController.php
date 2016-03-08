<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/api/v1/organizations")
 * @Security("has_role('ROLE_ORGANIZATION_SCOPE')")
 */
class OrganizationApiController extends RestController
{
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
