<?php

namespace AppBundle\Controller\Api\v1;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Cvele\MultiTenantBundle\Model\TenantAwareEntityInterface;
use AppBundle\Entity\AttachableEntityInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Each entity controller must extends this class.
 *
 * @abstract
 */
abstract class RestController extends Controller {

    /**
     * Method returns http methods that are supported by controller
     *
     * @abstract
     * @return array
     */
    protected function implementsMethods()
    {
      return ['GET', 'POST', 'PUT', 'DELETE'];
    }

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
    * @Route("/", name="api_users")
    * @Method({"GET"})
    */
    public function listAction()
    {
        $list = $this->getRepository()
            ->createQueryBuilder('e')
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return new JsonResponse($list, 200);
    }

    /**
    * Base "read" action.
    *
    * @param int $id
    * @return JsonResponse|NotFoundHttpException
    *
    * @Route("/{id}")
    * @Method({"GET"})
    */
    public function readAction($id)
    {
        $entityInstance = $this->getEntityForJson($id);
        if (false === $entityInstance) {
            return $this->createNotFoundException();
        }

        return new JsonResponse($entityInstance, 200);
    }

    /**
    * Base "create" action.
    *
    * @return JsonResponse|NotFoundHttpException
    *
    * @Route("/")
    * @Method({"POST"})
    */
    public function createAction()
    {
        $json = $this->getJsonFromRequest();
        if (false === $json) {
            throw new \Exception('Invalid JSON');
        }

        $object = $this->updateEntity($this->getNewEntity(), $json);
        if (false === $object) {
            throw new \Exception('Unable to create the entity');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($object); /* tenant will be added via tenant listener */
        $em->flush();

        return new JsonResponse($this->getEntityForJson($object->getId()), 201);
    }

    /**
     * Base "attach" action.
     *
     * @return JsonResponse|NotFoundHttpException
     *
     * @Route("/{id}/attach/{fileId}")
     * @Method({"PUT"})
     */
    public function attachAction($id, $fileId)
    {
        $object = $this->getEntity($id);
        if (false === $object) {
            return $this->createNotFoundException("Entity not found.");
        }

        if (!($object instanceof AttachableEntityInterface)) {
          throw new HttpException(405, 'Attach method is not supported on this entity.');
        }

        $fileManager = $this->get('app.manager.file');
        $file = $fileManager->getRepo()->find($fileId);
        if (false === $file) {
            return $this->createNotFoundException("File not found.");
        }

        $fileManager->attach($file, $object);

        return new JsonResponse([], 204);
    }


    /**
     * Base "update" action.
     *
     * @return JsonResponse|NotFoundHttpException
     *
     * @Route("/{id}")
     * @Method({"PUT"})
     */
    public function updateAction($id)
    {
        $object = $this->getEntity($id);
        if (false === $object) {
            return $this->createNotFoundException();
        }

        $json = $this->getJsonFromRequest();
        if (false === $json) {
            throw new \Exception('Invalid JSON');
        }

        if (false === $this->updateEntity($object, $json)) {
            throw new \Exception('Unable to update the entity');
        }

        $this->getDoctrine()->getManager()->flush($object);

        return new JsonResponse($this->getEntityForJson($object->getId()), 200);
    }

    /**
     * Base "delete" action.
     *
     * @return JsonResponse|NotFoundHttpException
     *
     * @Route("/{id}")
     * @Method({"DELETE"})
     */
    public function deleteAction($id)
    {
        $object = $this->getEntity($id);
        if (false === $object) {
            return $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return new Response([], 204);
    }

    /**
     * Returns an entity from its ID, or FALSE in case of error.
     *
     * @param int $id
     * @return Object|boolean
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

        try {
            return $this->getRepository()->findBy($filters);
        }
        catch (NoResultException $ex) {
            return false;
        }

        return false;
    }

    /**
     * Returns an entity from its ID as an associative array, or FALSE in case of error.
     *
     * @param int $id
     * @return array|boolean
     */
    protected function getEntityForJson($id)
    {
        $filters = ['id' => $id];
        if ($this->getNewEntity() instanceof TenantAwareEntityInterface) {
            $tenant = $this->get('multi_tenant.helper')->getCurrentTenant();
            if ($tenant === null) {
              throw $this->createAccessDeniedException('User not authorized or missing tenant.');
            }
            $filters['tenant'] = $tenant->getId();
        }

        try {
            $query = $this->getRepository()->createQueryBuilder('e')
                            ->where('e.id = :id');

            if (isset($filters['tenant'])) {
              $query->andWhere('e.tenant = :tenant')
                ->setParameter('tenant', $tenant);
            }

            $query->setParameter('id', $id)
                ->getQuery()->getSingleResult(Query::HYDRATE_ARRAY);
        }
        catch (NoResultException $ex) {
            return false;
        }

        return false;
    }

    /**
     * Returns the request's JSON content, or FALSE in case of error.
     *
     * @return string|boolean
     */
    protected function getJsonFromRequest()
    {
        $json = $this->get("request")->getContent();
        if (!$json) {
            return false;
        }

        return $json;
    }

    /**
     * Updates an entity with data from a JSON string.
     * Returns the entity, or FALSE in case of error.
     *
     * @param Object $entity
     * @param string $json
     * @return Object|boolean
     */
    protected function updateEntity($entity, $json)
    {
        if ($this->get('multi_tenant.helper')->isTenantObjectOwner($entity) === false) {
            throw $this->createAccessDeniedException('Current user has no access rights for this object.');
        }

        $data = json_decode($json);
        if ($data === null) {
            return false;
        }

        foreach ($data as $name => $value) {
            if ($name != 'id') {
                $setter = 'set' . ucfirst($name);
                if (method_exists($entity, $setter)) {
                    call_user_func_array(array($entity, $setter), array($value));
                }
            }
        }

        return $entity;
    }
}
