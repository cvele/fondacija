<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
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
     * @ORM\OneToMany(targetEntity="Company", mappedBy="user")
     */
    private $companies;

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="user")
     */
    private $documents;

    use TimestampableEntity;

    /**
     * @var \Doctrine\Common\Collections\Collection|UserTenants[]
     *
     * @ORM\ManyToMany(targetEntity="Tenant", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(
     *  name="tenant_users",
     *  joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="tenant_id", referencedColumnName="id")
     *  }
     * )
     */
    protected $userTenants;

    /**
     * @ORM\OneToOne(targetEntity="Invitation")
     * @ORM\JoinColumn(referencedColumnName="code")
     * @Assert\NotNull(message="Your invitation is wrong", groups={"Registration"})
     */
    protected $invitation;

    public function setInvitation(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function getInvitation()
    {
        return $this->invitation;
    }

    /**
     * Default constructor, initializes collections
     */
    public function __construct()
    {
        parent::__construct();
        $this->userTenants = new ArrayCollection();
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
     * Gets the value of companies.
     *
     * @return mixed
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * Gets the value of documents.
     *
     * @return mixed
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    public function setTenant(Tenant $tenant)
    {
        $this->addUserTenant($tenant);
    }

    public function getTenant()
    {
        return $this->userTenants[0];
    }

    public function getUserTenants()
    {
        return $this->userTenants;
    }

    /**
     * @param Tenant $tenant
     */
    public function addUserTenant(Tenant $tenant)
    {
        if ($this->userTenants->contains($tenant)) {
            return;
        }
        $this->userTenants->add($tenant);
        $tenant->addUser($this);
    }
    /**
     * @param Tenant $tenant
     */
    public function removeUserGroup(Tenant $tenant)
    {
        if (!$this->userTenants->contains($tenant)) {
            return;
        }
        $this->userTenants->removeElement($tenant);
        $tenant->removeUser($this);
    }
}