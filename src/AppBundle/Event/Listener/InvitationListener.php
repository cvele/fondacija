<?php

namespace AppBundle\Event\Listener;

use Symfony\Component\EventDispatcher\Event;
use AppBundle\Entity\Invitation;

class InvitationListener
{
    protected $templating;

    protected $tenantHelper;

    protected $mandrillDispatcher;

    protected $mandrillMessage;

    public function __construct($templating, $tenantHelper, $mandrillDispatcher, $mandrillMessage)
    {
        $this->templating          = $templating;
        $this->tenantHelper        = $tenantHelper;
        $this->mandrillDispatcher  = $mandrillDispatcher;
        $this->mandrillMessage     = $mandrillMessage;
    }

    public function sendInvitationEmail(Event $event)
    {
      $invitation = $event->getEntity();

      if (!($invitation instanceof Invitation)) {
        return;
      }

      $html = $this->templating->render('AppBundle:Email:invitation.html.twig', [
              'invitation' => $invitation,
              'user'       => $this->getUser(),
              // @TODO fix url
              'permalink'  => $this->generateUrl('user_signup_with_invite', [], true) . "?invitation=" . $invitation->getCode()
          ]);

      $message =
      $this->mandrillMessage
          ->setFromName($tenantHelper->getCurrentTenant()) // @TODO let tenant owner define this in settings?
          ->addTo($invitation->getEmail())
          ->setSubject('Invitation from ' . $invitation->getTenant())
          ->setHtml($html);

      $mandrillDispatcher->send($message);
    }
}
