<?php

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use AppBundle\Annotation\RequireTenant;
use AppBundle\Annotation\REST;

use FOS\UserBundle\Model\UserInterface;

/**
 * @Route("/api/v1/users")
 * @REST("app.manager.user")
 */
class UserApiController extends RestController
{

    /**
     * Base "attach" action.
     *
     * @return JsonResponse
     *
     * @Route("/{id}/attach_avatar/{fileId}")
     * @Method({"LINK"})
     * @RequireTenant
     */
    public function attachAvatarAction(Request $request, $id, $fileId)
    {
        $entity = $this->manager->findById($id)->getSingleResult();

        $fileManager = $this->get('app.manager.file');
        $file = $fileManager->findById($fileId)->getSingleResult();
        if (false === $file) {
            throw $this->createNotFoundException("File not found.");
        }
        $fileManager->attachUserAvatar($file, $entity, 'app.avatar.attached');

        return $this->response([], JsonResponse::HTTP_NO_CONTENT, $request);
    }


    /**
     * Base "deattach" action.
     *
     * @return JsonResponse
     *
     * @Route("/{id}/deattach_avatar/{fileId}")
     * @Method({"UNLINK"})
     * @RequireTenant
     */
    public function deattachAvatarAction(Request $request, $id, $fileId)
    {
        $entity = $this->manager->findById($id)->getSingleResult();

        $fileManager = $this->get('app.manager.file');
        $file = $fileManager->findById($fileId)->getSingleResult();
        if (false === $file) {
            throw $this->createNotFoundException("File not found.");
        }
        $fileManager->deattachUserAvatar($file, $entity);

        return $this->response([], JsonResponse::HTTP_NO_CONTENT, $request);
    }

    /**
    * @Route("/{id}")
    * @Method({"GET"})
    * @RequireTenant
    */
    public function readAction(Request $request, $id)
    {
        if ($id === 'me') {
          $user = $this->get('security.token_storage')->getToken()->getUser();
          if ($user === null) {
            $this->createNotFoundException('User not found');
          }
          $id = $user->getId();
        }
        return parent::readAction($request, $id);
    }

    /**
    * @Route("/invitation_register")
    * @Method({"POST"})
    */
    public function registerWithInvitationAction(Request $request)
    {
        $data = $this->parseRequest($request);

        $userManager = $this->get('fos_user.user_manager');
        $invitationManager = $this->get('app.manager.invitation');

        $invitation = $invitationManager
        ->getRepo()
        ->findOneBy([
            'code' => $data['code'],
            'email' => $data['email']
        ]);

        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setUsername($data['email']);
        $user->setEmail($invitation->getEmail());
        $user->setInvitation($invitation);
        $user->setPlainPassword($data['password']);
        $user->addUserTenant($invitation->getTenant());
        $userManager->updatePassword($user);
        $userManager->updateUser($user);

        return $this->response($user, 201, $request);
    }

    /**
    * @Route("/change_password")
    * @Method({"PUT"})
    */
    public function changePasswordAction(Request $request)
    {
        $data = $this->parseRequest($request, true);

        if (!isset($data['oldPassword']) || !isset($data['newPassword'])) {
            throw new HttpException(400, "Fields must not be blank");
        }

        $user = $this->getUser();
        $userManager = $this->get('fos_user.user_manager');
        $encoder = $userManager->getPasswordEncoder($user);
        if ($encoder->encodePassword($data['oldPassword'], $user->getSalt()) !== $user->getPassword()) {
            throw new HttpException(400, "Wrong current password. Try again?");
        }

        $user->setPlainPassword($data['newPassword']);
        $userManager->updatePassword($user);
        $userManager->updateUser($user);

        return $this->response([], 204, $request);
    }


    /**
    * @Route("/send_reset_email")
    * @Method({"POST"})
    */
    public function sendResetEmailAction(Request $request)
    {
        $data = $this->parseRequest($request, true); //bypass validation

        if (!isset($data['email']) || !isset($data['resetUrl'])) {
          throw new HttpException(400, "Email address cannot be blank.");
        }

        $email = $data['email'];
        $resetUrl = $data['resetUrl'];

        /** @var $user UserInterface */
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($email);

        if (null === $user) {
          throw $this->createNotFoundException("Resource not found.");
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
          throw new HttpException(400, "Password reset already requested. Try again later.");
        }

        if (null === $user->getConfirmationToken()) {
          /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
          $tokenGenerator = $this->get('fos_user.util.token_generator');
          $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->get('fos_user.mailer')->sendResettingEmailMessageApi($user, $resetUrl);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);

        return $this->response([], JsonResponse::HTTP_NO_CONTENT, $request);
    }

    /**
    * @Route("/reset_password")
    * @Method({"POST"})
    */
    public function resetAction(Request $request)
    {
        $data = $this->parseRequest($request, true); //bypass validation

        $token = $data['token'];
        $plainPassword = $data['plainPassword'];
        if (!isset($data['plainPassword'])) {
          throw new HttpException(400, "Password cannot be blank.");
        }

        if (!isset($data['token'])) {
          throw new HttpException(400, "Confirmation token is invalid or has expired.");
        }

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
          throw $this->createNotFoundException(sprintf('Confirmation token \'%s\' is invalid or has expired.', $token));
        }

        $user->setPlainPassword($plainPassword);
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $userManager->updatePassword($user);
        $userManager->updateUser($user);

        return $this->response([], JsonResponse::HTTP_NO_CONTENT, $request);
    }

}
