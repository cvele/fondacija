<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation\REST;
use AppBundle\Annotation\RequireTenant;

/**
 * @Route("/api/v1/files")
 * @REST("app.manager.file")
 */
class FileApiController extends RestController
{
    /**
    * @Route("/")
    * @Method({"POST"})
    * @RequireTenant
    */
    public function createAction(Request $request)
    {
        $uploadedFile = $this->get("request")->files->get('file', null);

        if (null === $uploadedFile || !($uploadedFile instanceof UploadedFile)) {
            throw new \HttpException(400, 'Invalid argument');
        }

        $fileManager = $this->get('app.manager.file');
        $file = $fileManager->createClass();
        $fileManager->save($file, $uploadedFile);

        return new JsonResponse($this->getEntityForJson($file->getId()), 201);
    }

    /**
     * @Route("/{id}")
     * @Method({"DELETE"})
     * @RequireTenant
     */
    public function deleteAction(Request $request, $id)
    {
        $object = $this->getEntity($id);
        if (false === $object) {
            return $this->createNotFoundException();
        }
        $fileManager = $this->get('app.manager.file');
        $fileManager->delete($object);
        return new Response([], 204);
    }
}
