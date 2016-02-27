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
 * @Route("/app/settings")
 */
class SettingsController extends Controller
{

    /**
     * @Route("/users", name="user_settings")
     * @Template("AppBundle:Settings:user_list.html.twig")
     */
    public function userListAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em     = $this->getDoctrine()->getManager();
        $tenant = $this->get('multi_tenant.helper')->getCurrentTenant();

        return ['tenant' => $tenant];

    }
}
