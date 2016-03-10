<?php

namespace AppBundle\Controller\Api\v1;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Cvele\MultiTenantBundle\Model\TenantAwareEntityInterface;
use AppBundle\Entity\AttachableEntityInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use AppBundle\Annotation\RequireTenant;

/**
 * Each entity controller must extends this class.
 *
 * @abstract
 */
abstract class RestController extends Controller
{
    /**
     * This method should return the entity's repository.
     *
     * @abstract
     * @return EntityRepository
     */
    abstract protected function getRepository();

    /**
     * This method should return a new entity instance to be used for the "create" action.
     *
     * @abstract
     * @return Object
     */
    abstract protected function getNewEntity();

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
        $parameters = $request->query->all();
        $filters = [];
        if ($this->getNewEntity() instanceof TenantAwareEntityInterface) {
            $tenant = $this->get('multi_tenant.helper')->getCurrentTenant();
            if ($tenant === null) {
              throw $this->createAccessDeniedException('User not authorized or missing tenant.');
            }
            $filters['tenant'] = $tenant->getId();
        }

        $list = $this->getRepository()->findBy($filters);

        return $this->response($list, 200, $request);
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
    public function readAction($id)
    {
        $request = $this->get('request');
        return $this->response($this->getEntity($id), 200, $request);
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
        $payload = $this->parseRequest($request);

        $object = $this->updateEntity($this->getNewEntity(), $payload);
        if (false === $object) {
            throw new HttpException(500, 'Unable to create the entity');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($object); /* tenant will be added via tenant listener */
        $em->flush();

        return $this->response($this->getEntity($object->getId()), 201);
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
        $object = $this->getEntity($id);

        if (!($object instanceof AttachableEntityInterface)) {
          throw new HttpException(405, 'Attach method is not supported on this entity.');
        }

        $fileManager = $this->get('app.manager.file');
        $file = $fileManager->getRepo()->find($fileId);
        if (false === $file) {
            throw $this->createNotFoundException("File not found.");
        }

        $fileManager->attach($file, $object);

        return $this->response([], 204);
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
        $object = $this->getEntity($id);
        $payload = $this->parseRequest($request);

        if (false === $this->updateEntity($object, $payload)) {
            throw new HttpException(500, 'Unable to update the entity');
        }

        $this->getDoctrine()->getManager()->flush($object);

        return $this->response($this->getEntity($object->getId()), 200);
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
        $object = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->response([], 204);
    }

    /**
     * Returns an entity from its ID or FALSE in case of error.
     *
     * @param int $id
     * @return mixed
     */
    protected function getEntity($id)
    {
        $filters = ['id' => $id];
        if ($this->getNewEntity() instanceof TenantAwareEntityInterface) {
            $tenant = $this->get('multi_tenant.helper')->getCurrentTenant();
            if ($tenant === null) {
              throw $this->createAccessDeniedException('User not authorized or missing tenant.');
            }
            $filters['tenant'] = $tenant->getId();
        }

        $entity = $this->getRepository()->findOneBy($filters);
        if ($entity === null) {
            throw $this->createNotFoundException("Entity not found.");
        }

        return $entity;
    }


    /**
     * Updates an entity with data from a JSON string.
     * Returns the entity, or FALSE in case of error.
     *
     * @param Object $entity
     * @param string $requestData
     * @return Object|boolean
     */
    protected function updateEntity($entity, $requestData)
    {
        $em = $this->getDoctrine()->getManager();
        if ($em->contains($entity) === false) { // this is not a new entity, preform tenant owner check
            if ($entity instanceof TenantAwareEntityInterface) {
                if ($this->get('multi_tenant.helper')->isTenantObjectOwner($entity) === false) {
                    throw $this->createAccessDeniedException('User is not allowed to modify object.');
                }
            }
        }

        $entityInstance = new $entity;
        foreach ($requestData as $name => $value) {
            if ($name != 'id' && $name != 'tenant') {
                $setter = 'set' . ucfirst($name);
                if (method_exists($entity, $setter)) {
                    $entityInstance->$setter($value);
                }
            }
        }

        return $entityInstance;
    }
    /**
     * Returns the parsed payload.
     *
     * @throws HttpException
     * @return string|boolean
     */
    protected function parseRequest(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        $this->validateRequest($payload, $request->getMethod());

        $error = json_last_error();
		if ($error && $error !== JSON_ERROR_NONE) {
			throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, sprintf("Invalid json payload supplied. Error: '%s'", (string) $error));
		}

        if (!is_array($payload) && !is_object($payload)) {
			throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, "Invalid json payload supplied. Error: 'JSON must be array or object'. '%s'", $this->get("request")->getContent());
		}

        return $payload;
    }

    private function validateRequest($payload, $httpMethod)
    {
        $function = new \ReflectionClass($this->getNewEntity());
        $validatorName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $function->getShortName()));
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
    protected function response($data, $httpCode = 200, $request = null)
    {
        $data =
        $this->get('app.rest_response')
            ->createResponseArray($data, $this->getNewEntity(), $request->query->get('include', []));

        return new JsonResponse($data, $httpCode);
    }
}
