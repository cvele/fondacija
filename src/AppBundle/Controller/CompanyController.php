<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Company;

/**
 * @Route("/api/company")
 */
class CompanyController extends RestController
{
    /**
     * @Route("", name="api_company_list")
     * @Method({"GET"})
     */
    public function listAction()
    {
        return parent::listAction();
    }

    /**
     * @Route("/{id}", name="api_company_read")
     * @Method({"GET"})
     */
    public function readAction($id)
    {
        return parent::readAction($id);
    }

    /**
     * @Route("", name="api_company_create")
     * @Method({"POST"})
     */
    public function createAction()
    {
        return parent::createAction();
    }

    /**
     * @Route("/{id}", name="api_company_update")
     * @Method({"PUT"})
     */
    public function updateAction($id)
    {
        return parent::updateAction($id);
    }

    /**
     * @see RestController::getRepository()
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Company');
    }

    /**
     * @see RestController::getNewEntity()
     * @return Object
     */
    public function getNewEntity()
    {
        return new Company();
    }
}
