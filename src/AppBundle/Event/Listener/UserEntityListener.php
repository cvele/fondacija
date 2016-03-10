<?php

namespace AppBundle\Event\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\CreatorAwareInterface;
use Symfony\Component\HttpFoundation\Session\Session;

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

      $user = $this->tokenStorage->getToken()->getUser();
      if (empty($user)) {
        return;
      }

      $entityManager = $args->getEntityManager();

      $entity->setUser($user);
  }
}
