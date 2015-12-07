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
        $results = $finder->find($request->query->get('term')."*");

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
     * @Route("/app", name="dashboard")
     * @Template("AppBundle::dashboard.html.twig")
     */
    public function dashboardAction(Request $request)
    {
        $personsNm = $this->getDoctrine()
                    ->getRepository('AppBundle:Person')
                    ->findNum();

        $companiesNm = $this->getDoctrine()
                    ->getRepository('AppBundle:Company')
                    ->findNum();

        $documentsNm = $this->getDoctrine()
                    ->getRepository('AppBundle:Document')
                    ->findNum();

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
