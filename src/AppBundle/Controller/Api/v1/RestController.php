<?php

namespace AppBundle\Controller\Api\v1;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\AttachableEntityInterface;
use AppBundle\Annotation\RequireTenant;

/**
 * Each entity controller must extends this class.
 *
 * @abstract
 */
abstract class RestController extends Controller
{
    /**
    * Base "list" action.
    *
    * @return JsonResponse
    *
    * @Route("/")
    * @Method({"GET"})
    * @RequireTenant
    */
    public function listAction(Request $request)
    {
        return $this->response($this->manager->findAll(), JsonResponse::HTTP_OK, $request);
    }

    /**
    * Base "read" action.
    *
    * @param int $id
    * @return JsonResponse
    *
    * @Route("/{id}")
    * @Method({"GET"})
    * @RequireTenant
    */
    public function readAction(Request $request, $id)
    {
        try {
            $result = $this->manager
                            ->findById($id)
                            ->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            throw $this->createNotFoundException("Resource not found.");
        }
        return $this->response($result, JsonResponse::HTTP_OK, $request);
    }

    /**
    * Base "create" action.
    *
    * @return JsonResponse
    *
    * @Route("/")
    * @Route("")
    * @Method({"POST"})
    * @RequireTenant
    */
    public function createAction(Request $request)
    {
        $entity = $this->manager->createClass();
        $payload = $this->parseRequest($request);
        $entity = $this->manager->applyPayloadToEntity($entity, $payload);
        $this->manager->save($entity);

        return $this->response($entity, JsonResponse::HTTP_CREATED, $request);
    }

    /**
     * Base "attach" action.
     *
     * @return JsonResponse
     *
     * @Route("/{id}/attach/{fileId}")
     * @Method({"PUT"})
     * @RequireTenant
     */
    public function attachAction(Request $request, $id, $fileId)
    {
        $entity = $this->manager->findById($id);

        if (!$entity instanceof AttachableEntityInterface) {
          throw new HttpException(JsonResponse::HTTP_METHOD_NOT_ALLOWED, 'Attach method is not supported on this entity.');
        }

        $fileManager = $this->get('app.manager.file');
        $file = $fileManager->findById($fileId);
        if (false === $file) {
            throw $this->createNotFoundException("File not found.");
        }
        $fileManager->attach($file, $object);

        return $this->response([], JsonResponse::HTTP_NO_CONTENT, $request);
    }


    /**
     * Base "update" action.
     *
     * @return JsonResponse
     *
     * @Route("/{id}")
     * @Method({"PUT"})
     * @Method({"PATCH"})
     * @RequireTenant
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->manager->findById($id);
        $payload = $this->parseRequest($request);
        $entity = $this->manager->applyPayloadToEntity($entity, $payload);
        $this->manager->save($entity);

        return $this->response($this->getEntity($entity->getId()), JsonResponse::HTTP_OK, $request);
    }

    /**
     * Base "delete" action.
     *
     * @return JsonResponse
     *
     * @Route("/{id}")
     * @Method({"DELETE"})
     * @RequireTenant
     */
    public function deleteAction(Request $request, $id)
    {
        try {
            $entity = $this->manager->findById($id)->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            throw $this->createNotFoundException("Resource not found.");
        }
        $this->manager->delete($entity);

        return $this->response([], JsonResponse::HTTP_NO_CONTENT, $request);
    }

    /**
     * Returns the parsed payload.
     *
     * @throws HttpException
     * @return string|boolean
     */
    protected function parseRequest(Request $request)
    {
        $payload = $request->getJsonPayload();
        $this->validateRequest($payload, $request->getMethod());

        $entityManger = $this->getDoctrine()->getManager();
        $entityClassName = $this->manager->getClassName();
        $apcKey = "entity_metadata_" . $entityClassName;
        if (apc_exists($apcKey) === false) {
            $entityMetadata = $entityManger->getClassMetadata($entityClassName);
            apc_add($apcKey, $entityMetadata, 0);
        } else {
            $entityMetadata = apc_fetch($apcKey);
        }
        foreach ($payload as $parameter => $value) {
            $apcKey = "entity_assoc_class_" . $entityClassName . "_" . $parameter;
            if (apc_exists($apcKey) === false) {
                try {
                    $fieldTargetClass = $entityMetadata->getAssociationTargetClass($parameter);
                    apc_add($apcKey, $fieldTargetClass, 0);
                } catch (\InvalidArgumentException $e) {
                    apc_add($apcKey, null, 0);
                    continue;
                }
            } else {
                $fieldTargetClass = apc_fetch($apcKey);
            }

            if ($fieldTargetClass === false || $fieldTargetClass === null) {
                continue;
            }

            $targetEntity = $entityManger->getRepository($fieldTargetClass)->find($value);
            $payload[$parameter] = $targetEntity;
        }

        return $payload;
    }

    private function validateRequest($payload, $httpMethod)
    {
        $apcKey = $this->manager->getClassName() . "_entity_validator";
        if (apc_exists($apcKey) === false) {
            $function = new \ReflectionClass($this->manager->createClass());
            $validatorName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $function->getShortName()));
            apc_add($apcKey, $validatorName, 0);
        } else {
            $validatorName = apc_fetch($apcKey);
        }

        $this->get('app.validator.'.$validatorName)
            ->setHttpMethod($httpMethod)
            ->setPayload($payload)
            ->validate();
    }

    /**
     * Returns JsonResponse object for collection or entity
     *
     * @param mixed $data
     * @param integer $httpCode
     * @return JsonResponse
     */
    protected function response($data, $httpCode = JsonResponse::HTTP_OK, $request)
    {
        $data =
        $this->get('app.rest_response')
            ->createResponseArray(
                $data,
                $this->manager->createClass(),
                $request
            );

        return new JsonResponse($data, $httpCode);
    }
}
