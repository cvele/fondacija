<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Cvele\MultiTenantBundle\Model\Tenant as BaseTenant;
/**
 * Tenant
 *
 * @ORM\Table(name="tenants")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\TenantRepository")
 */
class Tenant extends BaseTenant
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
