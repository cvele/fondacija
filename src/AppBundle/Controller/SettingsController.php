<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Company;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Pagerfanta\Pagerfanta;

/**
 * @Route("/")
 */
class DefaultController extends Controller
{

    /**
     * @Route("/app/tenants", name="pick_tenant")
     * @Template("AppBundle::pick_tenant.html.twig")
     */
    public function pickTenantAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        return ['tenants' => $user->getUserTenants()];

    }
}
