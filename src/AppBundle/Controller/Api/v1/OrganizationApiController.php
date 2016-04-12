<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Annotation\REST;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation\RequireTenant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/v1/organizations")
 * @REST("app.manager.organization")
 */
class OrganizationApiController extends RestController
{

    /**
    * Base "list" action.
    *
    * @return JsonResponse
    *
    * @Route("")
    * @Method({"GET"})
    * @RequireTenant
    */
    public function listAction(Request $request)
    {
        $sortDirection = $request->query->get('direction', 'desc');
        $q = $request->query->get('q', null);

        $tenantHelper = $this->get('multi_tenant.helper');

        /** var FOS\ElasticaBundle\Manager\RepositoryManager */
        $repositoryManager = $this->get('fos_elastica.manager');

        /** var FOS\ElasticaBundle\Repository */
        $repository = $repositoryManager->getRepository('AppBundle:Organization');

        $organizations = $repository->findWithTenant($q,
                                        $tenantHelper->getCurrentTenant()->getId(),
                                        $sortDirection
                                        );

        return $this->response($organizations, JsonResponse::HTTP_OK, $request);
    }

    /**
     * Base "attach" action.
     *
     * @return JsonResponse
     *
     * @Route("/{id}/attach_logo/{fileId}")
     * @Method({"LINK"})
     * @RequireTenant
     */
    public function attachLogoAction(Request $request, $id, $fileId)
    {
        $entity = $this->manager->findById($id)->getSingleResult();

        $fileManager = $this->get('app.manager.file');
        $file = $fileManager->findById($fileId)->getSingleResult();
        if (false === $file) {
            throw $this->createNotFoundException("File not found.");
        }
        $fileManager->attachOrganizationLogo($file, $entity, 'app.organization_logo.attached');

        return $this->response([], JsonResponse::HTTP_NO_CONTENT, $request);
    }


    /**
     * Base "deattach" action.
     *
     * @return JsonResponse
     *
     * @Route("/{id}/deattach_logo/{fileId}")
     * @Method({"UNLINK"})
     * @RequireTenant
     */
    public function deattachLogoAction(Request $request, $id, $fileId)
    {
        $entity = $this->manager->findById($id)->getSingleResult();

        $fileManager = $this->get('app.manager.file');
        $file = $fileManager->findById($fileId)->getSingleResult();
        if (false === $file) {
            throw $this->createNotFoundException("File not found.");
        }
        $fileManager->deattachOrganizationLogo($file, $entity);

        return $this->response([], JsonResponse::HTTP_NO_CONTENT, $request);
    }
}
