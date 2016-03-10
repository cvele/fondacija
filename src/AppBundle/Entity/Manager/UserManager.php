<?php

namespace AppBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use FOS\UserBundle\Util\CanonicalizerInterface;
use AppBundle\Entity\Traits\ObjectManagerTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserManager extends BaseUserManager
{
	use ObjectManagerTrait {
        __construct as traitConstruct;
	}

    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        CanonicalizerInterface $usernameCanonicalizer,
        CanonicalizerInterface $emailCanonicalizer,
        EntityManager $em,
        EventDispatcherInterface $dispatcher,
        $tenantHelper,
        $class
    ) {

        $this->traitConstruct($dispatcher, $em, $tenantHelper, $class);
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer, $em, $class);
    }
}
