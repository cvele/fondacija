<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Event\UserEvent;
use AppBundle\Entity\Invitation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Hip\MandrillBundle\Message;

/**
 * @Route("/")
 */
class RegistrationController extends Controller
{

    /**
     * @Route("/app/user/create-invite", name="create_invitation")
     */
    public function createInvitationAction(Request $request)
    {
        $email = $request->request->get('email_for_invite');

        $userManager = $this->get('fos_user.user_manager');
        $user        = $userManager->findUserByEmail($email);

        if ($user != null) //existing user
        {
            return;
        }

        $em = $this->getDoctrine()->getManager();
        $invitation = new Invitation();
        $invitation->setEmail($email);
        $tenant = $this->get('multi_tenant.helper')->getCurrentTenant();
        $em->persist($invitation);
        $em->flush();

        $engine = $this->get('templating');
        $html = $engine->render('AppBundle:Email:invitation.html.twig', [
                'invitation' => $invitation,
                'user'       => $this->get('security.token_storage')->getToken()->getUser(),
                'permalink'  => $this->generateUrl('user_signup_with_invite', [], true) . "?invitation=" . $invitation->getCode()
            ]);

        $message = new Message();
        $message
            ->setFromName($request->getSession()->get('tenant'))
            ->addTo($invitation->getEmail())
            ->setSubject('Invitation from ' . $invitation->getTenant())
            ->setHtml($html);

        $result = $this->get('hip_mandrill.dispatcher')->send($message);

        return new JsonResponse(['success' => true, 'invitation' => $invitation->getCode(), 'email' => $email]);
    }

    /**
     * @Route("/client/signup/tenant", name="user_signup_with_tenant")
     * @Template("AppBundle:Registration:tenant_signup.html.twig")
     */
    public function registerWithTenantAction(Request $request)
    {
        return $this->register($request, 'app.form.tenant_registration');
    }

    /**
     * @Route("/client/signup/invite", name="user_signup_with_invite")
     * @Template("AppBundle:Registration:tenant_signup.html.twig")
     */
    public function registerWithInviteAction(Request $request)
    {
        return $this->register($request, 'app.form.registration.invite');
    }

    private function register(Request $request, $form_name)
    {
        $user = $this->getUser();
        if (is_object($user) and $user instanceof UserInterface)
        {
            $url = $this->generateUrl('dashboard');
            $response = new RedirectResponse($url);
            return $response;
        }

        $userManager = $this->get('fos_user.user_manager');
        $dispatcher  = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        // for registration with invite
        if ($request->query->get('invitation', null) != null) {
            $invitation = $this->getDoctrine()
                        ->getRepository('AppBundle:Invitation')
                        ->find(['code' => $request->query->get('invitation')]);

            if ($invitation !== null) {
                $user->setEmail($invitation->getEmail());
                $user->setInvitation($invitation);
                $user->addUserTenant($invitation->getTenant());
            }
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->get($form_name)->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $user->getUserTenants()->first()->setOwner($user);
            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return ['form'=>$form->createView()];
    }

    /**
     * Tell the user to check his email provider
     *
     * @Route("/check-email", name="fos_user_registration_check_email")
     * @Template("AppBundle:Registration:check_email.html.twig")
     */
    public function checkEmailAction()
    {
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');
        $this->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        return ['user' => $user];
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     *
     * @Route("/confirm/{token}", name="tenant_user_registration_confirm")
     */
    public function confirmAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user)
        {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed
     *
     * @Route("/confirmed", name="fos_user_registration_confirmed")
     */
    public function confirmedAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface)
        {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $url = $this->generateUrl('fos_user_security_logout');
        $response = new RedirectResponse($url);

        return $response;
    }
}
