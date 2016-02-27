<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Document;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Pagerfanta\Pagerfanta;

/**
 * @Route("/app/document")
 */
class DocumentController extends Controller
{
    /**
     * @Route("/delete-multiple", name="documents_delete_multiple", options={"expose"=true})
     */
    public function deleteDocumentsAction(Request $request)
    {
        $ids  = $request->request->get('ids');
        $em   = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()->getRepository('AppBundle:Document');

        if (!is_array($ids))
        {
            return new JsonResponse(['deleted' => []]);
        }

        $deleted = [];
        foreach ($ids as $key => $id) {
            $document = $repo->findOneBy(['id'=>$id]);

            if ($document == null)
            {
                continue;
            }

            if (!$this->get('multi_tenant.helper')->isTenantObjectOwner($document))
            {
                throw new AccessDeniedException();
            }

            unlink($this->container->getParameter('kernel.root_dir') . '/../web/uploads/document/'.$document->getFilename());

            $em->remove($document);
            $em->flush();

            $deleted[] = $id;
        }

        return new JsonResponse(['deleted' => $deleted]);
    }
    /**
     * @Route("/list", name="document_list")
     * @Template("AppBundle:Document:list.html.twig")
     */
    public function listDocumentsAction(Request $request)
    {
        $q = $this->getDoctrine()
                    ->getRepository('AppBundle:Document')
                    ->findAllQuery($this->get('session')->get('tenant_id'));

        $adapter = new DoctrineORMAdapter($q);

        $documents = new Pagerfanta($adapter);
        $documents->setMaxPerPage(20);

        return ['documents' => $documents];
    }

    /**
     * @Route("/create", name="document_create")
     * @Template("AppBundle:Document:create.html.twig")
     */
    public function documentCreate(Request $request)
    {
        return [];

    }

    /**
     * @Route("/{id}/download", name="document_download")
     * @ParamConverter("document", class="AppBundle:Document")
     */
    public function downloadDocumentAction(Document $document, Request $request)
    {
        if (!$this->get('multi_tenant.helper')->isTenantObjectOwner($document))
        {
            throw new AccessDeniedException();
        }

        $response = $this->get('igorw_file_serve.response_factory')
                        ->create('../web/uploads/document/'.$document->getFilename(),
                                $document->getMimeType(),
                                ['inline'=>false, 'serve_filename'=>$document->getOriginalFileName()]);
        return $response;
    }

    /**
     * @Route("/{id}/show", name="document_show", options={"expose"=true})
     * @Template("AppBundle:Document:show.html.twig")
     * @ParamConverter("document", class="AppBundle:Document")
     */
    public function showDocumentAction(Document $document, Request $request)
    {
        if (!$this->get('multi_tenant.helper')->isTenantObjectOwner($document))
        {
            throw new AccessDeniedException();
        }

        return ['document' => $document];
    }
}
