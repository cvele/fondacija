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
use Symfony\Component\Finder\Exception\AccessDeniedException;

/**
 * @Route("/app")
 */
class CompanyController extends Controller
{
    /**
     * @Route("/company", name="company_list")
     * @Template("AppBundle:Company:list.html.twig")
     */
    public function listCompanyAction(Request $request)
    {
        $companiesQuery = $this->getDoctrine()
                        ->getRepository('AppBundle:Company')
                        ->findAllQuery($this->get('session')->get('tenant'));

        $adapter = new DoctrineORMAdapter($companiesQuery);

        $companies = new Pagerfanta($adapter);
        $companies->setMaxPerPage(20);

        return ['companies' => $companies];
    }

    /**
     * @Route("/company/{id}/delete", name="company_delete")
     * @ParamConverter("company", class="AppBundle:Company")
     */
    public function deleteCompanyAction(Company $company, Request $request)
    {
        if (!$this->get('app.helper.tenant')->isTenantObjectOwner($company))
        {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($company);
        $em->flush();

        return $this->redirect($this->generateUrl(
                'company_list'
            ));
    }

    /**
     * @Route("/companies_delete", name="company_delete_multiple", options={"expose"=true})
     */
    public function deleteCompaniesAction(Request $request)
    {
        $ids  = $request->request->get('ids');
        $em   = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()->getRepository('AppBundle:Company');

        if (!is_array($ids))
        {
            return new JsonResponse(['deleted' => []]);
        }

        $deleted = [];
        foreach ($ids as $key => $id) {
            $company = $repo->findOneBy(['id'=>$id]);

            if ($company == null)
            {
                continue;
            }

            if (!$this->get('app.helper.tenant')->isTenantObjectOwner($company))
            {
                throw new AccessDeniedException();
            }

            $em->remove($company);
            $em->flush();

            $deleted[] = $id;
        }

        return new JsonResponse(['deleted' => $deleted]);
    }

    /**
     * @Route("/company/{id}/show", name="company_show")
     * @Template("AppBundle:Company:show.html.twig")
     * @ParamConverter("company", class="AppBundle:Company")
     */
    public function showCompanyAction(Company $company, Request $request)
    {
        if (!$this->get('app.helper.tenant')->isTenantObjectOwner($company))
        {
            throw new AccessDeniedException();
        }

        return ['company' => $company];
    }

    /**
     * @Route("/company/{id}/edit", name="company_edit")
     * @Template("AppBundle:Company:edit.html.twig")
     * @ParamConverter("company", class="AppBundle:Company")
     */
    public function editCompanyAction(Company $company, Request $request)
    {
        if (!$this->get('app.helper.tenant')->isTenantObjectOwner($company))
        {
            throw new AccessDeniedException();
        }

        $form = $this->get('app.form.company')->setData($company);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();

            return $this->redirect($this->generateUrl(
                'company_show',
                array('id' => $company->getId())
            ));
        }

        return ['form' => $form->createView(), 'company' => $company];
    }

    /**
     * @Route("/company/new", name="company_new")
     * @Template("AppBundle:Company:create.html.twig")
     */
    public function createCompanyAction(Request $request)
    {
        $company = new \AppBundle\Entity\Company();

        $form = $this->get('app.form.company')->setData($company);
        $form->handleRequest($request);

        $user = $this->get('security.context')->getToken()->getUser();

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $tenant = $em->getRepository('AppBundle:Tenant')->find($this->get('session')->get('tenant')->getId());
            $company->setUser($user);
            $company->setTenant($tenant);
            $em->persist($company);
            $em->flush();

            return $this->redirect($this->generateUrl(
                'company_show',
                array('id' => $company->getId())
            ));
        }

        return ['form'=>$form->createView()];
    }
}
