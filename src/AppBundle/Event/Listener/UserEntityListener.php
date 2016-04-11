<?php

namespace AppBundle\Event\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\CreatorAwareInterface;

class UserEntityListener
{
  private $tokenStorage;

  public function __construct($tokenStorage)
  {
    $this->tokenStorage = $tokenStorage;
  }

  public function prePersist(LifecycleEventArgs $args)
  {
      $entity = $args->getEntity();

      if (!$entity instanceof CreatorAwareInterface) {
        return;
      }

      if ($entity->getUser() !== null) {
          return;
      }

      $user = $this->tokenStorage->getToken()->getUser();
      if (empty($user)) {
          return;
      }

      $entity->setUser($user);
  }
}
