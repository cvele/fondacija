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
     * @Route("/get_usernames.json", name="get_usernames", options={"expose"=true})
     */
    public function getUsernamesAction(Request $request)
    {

        $finder = $this->container->get('fos_elastica.finder.app.user');

        $boolQuery = new \Elastica\Query\BoolQuery();

        $fieldQuery = new \Elastica\Query\MultiMatch();
        $fieldQuery->setQuery($request->query->get('term')."*");
        $fieldQuery->setFields(['_all']);
        $boolQuery->addMust($fieldQuery);

        $tenantQuery = new \Elastica\Query\Term();
        $tenantQuery->setTerm('id', $this->get('session')->get('tenant')->getId());


        $nestedQuery = new \Elastica\Query\Nested();
        $nestedQuery->setPath('tenant');
        $nestedQuery->setQuery($tenantQuery);
        $boolQuery->addMust($nestedQuery);

        $results = $finder->find($boolQuery);

        $gravatar = $this->get('templating.helper.gravatar');

        $users = [];
        foreach($results as $user)
        {
            $users[] = [
                        'value'  => $user->getUsername(),
                        'uid'    => 'user:' . $user->getId(),
                        'avatar' => $gravatar->getUrl($user->getEmail())
                    ];
        }

        return new JsonResponse($users);
    }

    /**
     * @Route("/app/tenants", name="pick_tenant")
     * @Template("AppBundle::pick_tenant.html.twig")
     */
    public function pickTenantAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        return ['tenants' => $user->getUserTenants()];

    }

    /**
     * @Route("/app", name="dashboard")
     * @Template("AppBundle::dashboard.html.twig")
     */
    public function dashboardAction(Request $request)
    {
        $personsNm = $this->getDoctrine()
                    ->getRepository('AppBundle:Person')
                    ->findNum($this->get('session')->get('tenant'));

        $companiesNm = $this->getDoctrine()
                    ->getRepository('AppBundle:Company')
                    ->findNum($this->get('session')->get('tenant'));

        $documentsNm = $this->getDoctrine()
                    ->getRepository('AppBundle:Document')
                    ->findNum($this->get('session')->get('tenant'));

        return ['companiesNm' => $companiesNm, 'personsNm' => $personsNm, 'documentsNm' => $documentsNm];

    }

    /**
     * @Route("/app/search", name="search")
     * @Template("AppBundle::search.html.twig")
     */
    public function searchAction(Request $request)
    {
        $q = $request->query->get('q');

        $finder = $this->container->get('fos_elastica.finder.app');

        $boolQuery = new \Elastica\Query\BoolQuery();

        $fieldQuery = new \Elastica\Query\MultiMatch();
        $fieldQuery->setQuery($q);
        $fieldQuery->setFields(['_all']);
        $boolQuery->addMust($fieldQuery);

        $tenantQuery = new \Elastica\Query\Term();
        $tenantQuery->setTerm('id', $this->get('session')->get('tenant')->getId());


        $nestedQuery = new \Elastica\Query\Nested();
        $nestedQuery->setPath('tenant');
        $nestedQuery->setQuery($tenantQuery);
        $boolQuery->addMust($nestedQuery);

        $results = $finder->findPaginated($boolQuery);

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
