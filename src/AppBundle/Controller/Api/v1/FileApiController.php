<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation\REST;
use AppBundle\Annotation\RequireTenant;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/v1/files")
 * @REST("app.manager.file")
 */
class FileApiController extends RestController
{
    /**
    * @Route("/")
    * @Route("")
    * @Method({"POST"})
    * @RequireTenant
    */
    public function createAction(Request $request)
    {
        $uploadedFile = $request->files->get('file', null);

        if (null === $uploadedFile || !($uploadedFile instanceof UploadedFile)) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, 'Invalid argument');
        }

        $fileManager = $this->get('app.manager.file');
        $file = $fileManager->createClass();
        $fileManager->save($file, $uploadedFile);

        return $this->response($file, JsonResponse::HTTP_CREATED, $request);
    }
}
