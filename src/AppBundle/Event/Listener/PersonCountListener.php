<?php

namespace AppBundle\Event\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Person;

class PersonCountListener
{

  public function postPersist(LifecycleEventArgs $args)
  {
      $entity = $args->getEntity();

      if (!$entity instanceof Person) {
          return;
      }

      $organization = $entity->getOrganization();
      if ($organization === null) {
          return;
      }

      $entityManager = $args->getEntityManager();
      if ($entityManager->contains($entity) === false) { // this is a new entity
          $organization->incrementNumPersons(); //will increment num of persons by +1
          $entityManager->persist($organization);
          $entityManager->flush();
      }
  }


  public function postRemove(LifecycleEventArgs $args)
  {
      $entity = $args->getEntity();

      if (!$entity instanceof Person) {
          return;
      }

      $organization = $entity->getOrganization();
      if ($organization === null) {
          return;
      }

      $entityManager = $args->getEntityManager();
      $organizatio->decrementNumPersons(); //will increment num of persons by -1
      $entityManager->persist($organization);
      $entityManager->flush();
  }
}
