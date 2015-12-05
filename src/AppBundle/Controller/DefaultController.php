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
     * @Route("/app", name="dashboard")
     * @Template("AppBundle::dashboard.html.twig")
     */
    public function dashboardAction(Request $request)
    {
        $persons = $this->getDoctrine()
                    ->getRepository('AppBundle:Person')
                    ->findAll();

        $companies = $this->getDoctrine()
                    ->getRepository('AppBundle:Company')
                    ->findAll();

        return ['companies' => $companies, 'persons' => $persons];

    }

    /**
     * @Route("/app/search", name="search")
     * @Template("AppBundle::search.html.twig")
     */
    public function searchAction(Request $request)
    {
        $q = $request->query->get('q');

        $finder = $this->container->get('fos_elastica.finder.app');
        $results = $finder->find($q);

        return ['results' => $results, 'query' => $q];

    }

    /**
     * @Route("/", name="home")
     */
    public function homeAction(Request $request)
    {
        return $this->redirect($this->generateUrl(
                'dashboard'
                ));
    }
}
