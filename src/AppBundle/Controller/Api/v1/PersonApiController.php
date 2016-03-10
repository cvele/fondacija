<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation\REST;
use AppBundle\Annotation\RequireTenant;

/**
 * @Route("/api/v1/persons")
 * @REST("app.manager.person")
 */
class PersonApiController extends RestController
{

    /**
    * @Route("/")
    * @Route("")
    * @Method({"POST"})
    * @RequireTenant
    */
    public function createAction(Request $request)
    {
        $entity = $this->getManager()->createClass();
        $payload = $this->parseRequest($request);
        $payload['organization'] = $this->get('app.manager.organization')->findById($payload['organization']);
        $entity = $this->updateEntity($entity, $payload);
        $this->getManager()->save($entity);

        return $this->response($entity, 201, $request);
    }


    /**
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

}
