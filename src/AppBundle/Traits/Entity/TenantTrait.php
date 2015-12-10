<?php

namespace AppBundle\Traits\Entity;

use AppBundle\Entity\Tenant;

trait TenantTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="Tenant", cascade={"all"})
     * @ORM\JoinColumn(name="tenant_id", referencedColumnName="id", nullable=true)
     */
    private $tenant;

    /**
     * Gets the value of tenant.
     *
     * @return mixed
     */
    public function getTenant()
    {
        return $this->tenant;
    }

    /**
     * Sets the value of tenant.
     *
     * @param mixed $tenant the tenant
     *
     * @return self
     */
    public function setTenant(Tenant $tenant)
    {
        $this->tenant = $tenant;

        return $this;
    }
}