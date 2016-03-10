<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v1/invitations")
 */
class InvitationApiController extends RestController
{
  /**
   * @Route("/invitation")
   * @Method({"POST"})
   */
  public function createInvitationAction(Request $request)
  {
      $tenantHelper = $this->get('multi_tenant.helper');

      if ($tenantHelper->isUserTenantOwner($this->getUser())) {
        throw $this->createAccessDeniedException('User not authorized to manage tenant.');
      }

      $userManager = $this->get('fos_user.user_manager');
      $invitationManager = $this->get('app.manager.invitation');

      $body = $this->getJsonFromRequest();

      $user = $userManager->findUserByEmail($body->email);

      if ($user !== null) { // this is an existing user and we should just add to tenant
        $tenantHelper->addUserToTenant($user, $tenantHelper->getCurrentTenant());
        return new JsonResponse([], 200);
      }

      $invitation = $invitationManager->createClass();
      $invitation->setEmail($email);
      $invitationManager->save($invitation);
      // Email is being dispatched via event listener

      return new JsonResponse($invitation, 201);
  }

  /**
   * @see RestController::getRepository()
   * @return EntityRepository
   */
  protected function getRepository()
  {
      return $this->get('app.manager.invitation')->getRepo();
  }

  /**
   * @see RestController::getNewEntity()
   * @return Object
   */
  protected function getNewEntity()
  {
      return $this->get('app.manager.invitation')->createClass();
  }
}
