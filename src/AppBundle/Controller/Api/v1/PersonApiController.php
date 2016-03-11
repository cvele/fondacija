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
}
