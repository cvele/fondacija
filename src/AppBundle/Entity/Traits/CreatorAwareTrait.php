<?php

namespace AppBundle\Entity\Traits;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Cvele\MultiTenantBundle\Model\TenantAwareEntityInterface;
use Cvele\MultiTenantBundle\Helper\TenantHelper;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Event\EntityEvent;

trait CreatorAwareTrait
{
	public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

}
