<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Annotation\REST;

/**
 * @Route("/api/v1/organizations")
 * @REST("app.manager.organization")
 */
class OrganizationApiController extends RestController
{
}
