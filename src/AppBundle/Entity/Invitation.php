<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cvele\MultiTenantBundle\Model\Traits\TenantAwareEntityTrait;
use Cvele\MultiTenantBundle\Model\TenantAwareEntityInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\InvitationRepository")
 * @ORM\Table(name="user_invitations")
 */
class Invitation implements TenantAwareEntityInterface, EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=6)
     */
    protected $code;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $email;

    /**
     * When sending invitation be sure to set this value to `true`
     *
     * It can prevent invitations from being sent twice
     *
     * @ORM\Column(type="boolean")
     */
    protected $sent = false;

    public $bypassTenantSecurity = true;

    use TenantAwareEntityTrait;

    public function __construct()
    {
        // generate identifier only once, here a 6 characters length code
        $this->code = substr(md5(uniqid(rand(), true)), 0, 6);
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function isSent()
    {
        return $this->sent;
    }

    public function send()
    {
        $this->sent = true;
    }
}
