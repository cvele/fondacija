<?php

namespace AppBundle\Controller\Api\v1;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
    protected $manager;

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
        return $this->response($this->getManager()->findAll(), 200, $request);
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
        return $this->response($this->getManager()->findById($id), 200, $request);
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
        $entity = $this->getManager()->createClass();
        $payload = $this->parseRequest($request);
        $entity = $this->updateEntity($entity, $payload);
        $this->getManager()->save($entity);

        return $this->response($entity, 201, $request);
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
        $entity = $this->getManager()->findById($id);

        if (!$entity instanceof AttachableEntityInterface) {
          throw new HttpException(405, 'Attach method is not supported on this entity.');
        }

        $fileManager = $this->get('app.manager.file');
        $file = $fileManager->findById($fileId);
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
    public function updateAction(Request $request, $id) //@TODO too meny queries here
    {
        $entity = $this->getManager()->findById($id);
        $payload = $this->parseRequest($request);
        $entity = $this->updateEntity($entity, $payload);
        $this->getManager()->save($entity);

        return $this->response($this->getEntity($entity->getId()), 200);
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
        $entity = $this->getManager()->findById($id);
        $this->getManager()->delete($entity);

        return $this->response([], 204);
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
        $function = new \ReflectionClass($this->getManager()->createClass());
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
            ->createResponseArray(
                $data,
                $this->getManager()->createClass(),
                $request->query->get('include', [])
            );

        return new JsonResponse($data, $httpCode);
    }

    public function setManager($manager)
    {
        $this->manager = $manager;
        return $this;
    }

    protected function getManager()
    {
        return $this->manager;
    }
}
