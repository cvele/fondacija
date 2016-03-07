<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/api/v1/users")
 */
class UserApiController extends RestController
{
  /**
   * @see RestController::scope()
   * @return string
   */
  public function scope()
  {
    return 'user';
  }

  /**
   * @Route("/{id}", name="api_users_read")
   * @Method({"GET"})
   */
  public function readAction($id)
  {
      if ($id === 'me') {
          $user = $this->get('security.token_storage')->getToken()->getUser();
          if ($user === null) {
            $this->createNotFoundException('User not found');
          }
          $id = $user->getId();
      }
      return parent::readAction($id);
  }

  public function createAction() {
      throw new HttpException(405, "User entity can be created only through register methods.");
  }

  /**
   * @Route("/invitation")
   * @Method({"POST"})
   */
  public function registerWithInvitationAction()
  {
      $json = $this->getJsonFromRequest();
      if (false === $json) {
          throw new \Exception('Invalid JSON');
      }

      $data = json_decode($json);
      $userManager = $this->get('fos_user.user_manager');
      $invitationManager = $this->get('app.manager.invitation');

      $invitation = $invitationManager->getRepo()->find(['code' => $data->invitation]);
      if (empty($invitation)) {
        throw new HttpException(400, "Invalid invitation code.");
      }

      $user = $userManager->createUser();
      $user->setEnabled(true);
      $user->setUsername($data->username);
      $user->setEmail($invitation->getEmail());
      $user->setInvitation($invitation);
      $user->setPlainPassword($data->password);
      $user->addUserTenant($invitation->getTenant());
      $userManager->updatePassword($user);
      $userManager->updateUser($user);

      return new JsonResponse($this->getEntityForJson($user->getId()), 201);
  }

  /**
   * @see RestController::getRepository()
   * @return EntityRepository
   */
  protected function getRepository()
  {
      return $this->getDoctrine()->getManager()->getRepository('AppBundle:User');
  }

  /**
   * @see RestController::getNewEntity()
   * @return Object
   */
  protected function getNewEntity()
  {
      return $this->get('fos_user.user_manager')->getClass();
  }
}
