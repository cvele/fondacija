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
 * @ORM\Entity
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
     * @ORM\OneToMany(targetEntity="Company", mappedBy="user")
     */
    private $companies;

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="user")
     */
    private $documents;

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
}