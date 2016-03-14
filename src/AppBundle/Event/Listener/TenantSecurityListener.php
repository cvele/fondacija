<?php

namespace AppBundle\Event\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Cvele\MultiTenantBundle\Model\TenantAwareEntityInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TenantSecurityListener
{
    protected $session;

    public function __construct($session) {
        $this->session = $session;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        return $this->check($args);
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        return $this->check($args);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        return $this->check($args);
    }

    protected function check(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof TenantAwareEntityInterface) {
            return;
        }

        if (isset($entity->bypassTenantSecurity) && $entity->bypassTenantSecurity === true) {
            return;
        }

        $entityManager = $args->getEntityManager();

        if ($entityManager->contains($entity) !== false) { // this is not a new entity, preform tenant owner check
            if ($entity->getTenant()->getId() !== $this->session->get('tenant_id')) {
                throw new AccessDeniedHttpException('User is not allowed to access object.');
            }
        }
    }
}
