<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Cvele\MultiTenantBundle\Model\Traits\TenantAwareUserTrait;
use Cvele\MultiTenantBundle\Model\TenantAwareUserInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends BaseUser implements TenantAwareUserInterface
{
    use TenantAwareUserTrait;
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Person", mappedBy="user")
     */
    private $persons;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="user")
     */
    private $files;

    /**
     * @ORM\OneToMany(targetEntity="Organization", mappedBy="user")
     */
    private $organizations;

    /**
     * @ORM\OneToOne(targetEntity="Invitation")
     * @ORM\JoinColumn(referencedColumnName="code")
     * @Assert\NotNull(message="Your invitation is wrong", groups={"Registration"})
     */
    protected $invitation;

    public function __construct()
    {
        parent::__construct();
        $this->setupTenantAwareUserTrait();
    }

    public function setInvitation(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function getInvitation()
    {
        return $this->invitation;
    }

    /**
     * Gets the value of persons.
     *
     * @return mixed
     */
    public function getPersons()
    {
        return $this->persons;
    }

    /**
     * Gets the value of files.
     *
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    public function toArray()
    {
      $tenants = [];
      foreach($this->getUserTenants() as $tenant) {
        $tenants[] = $tenant->toArray();
      }

      return [
        'id'        => $this->getId(),
        'username'  => $this->getUsername(),
        'tenants'   => $tenants,
        'email'     => $this->getEmail()
      ];
    }

    /**
     * Get the value of Organizations
     *
     * @return mixed
     */
    public function getOrganizations()
    {
        return $this->organizations;
    }
}
