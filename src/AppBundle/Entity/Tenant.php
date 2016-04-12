<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cvele\MultiTenantBundle\Model\Tenant as BaseTenant;

/**
 * Tenant
 * @ORM\Entity
 * @ORM\Table(name="tenants")
 */
class Tenant extends BaseTenant implements EntityInterface
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function toArray()
    {
      return [
        'id' => $this->getId(),
        'name' => $this->getName(),
        'owner' => $this->getOwner()->getId()
      ];
    }
}
