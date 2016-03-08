<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/api/v1/files")
 */
class FileApiController extends RestController
{
    /**
    * @Route("/")
    * @Method({"POST"})
    */
    public function createAction()
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
     */
    public function deleteAction($id)
    {
        $object = $this->getEntity($id);
        if (false === $object) {
            return $this->createNotFoundException();
        }
        $fileManager = $this->get('app.manager.file');
        $fileManager->delete($object);
        return new Response([], 204);
    }

    /**
     * @see RestController::getRepository()
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->get('app.manager.file')->getRepo();
    }

    /**
     * @see RestController::getNewEntity()
     * @return Object
     */
    protected function getNewEntity()
    {
        return $this->get('app.manager.file')->createClass();
    }
}
