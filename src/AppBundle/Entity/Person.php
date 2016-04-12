<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Cvele\MultiTenantBundle\Model\Traits\TenantAwareEntityTrait;
use Cvele\MultiTenantBundle\Model\TenantAwareEntityInterface;

/**
 * Person
 *
 * @ORM\Table(name="persons")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\PersonRepository")
 */
class Person implements TenantAwareEntityInterface, AttachableEntityInterface, CreatorAwareInterface, EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="personal_email", type="string", length=255, nullable=true)
     */
    private $personalEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="business_phone", type="string", length=255, nullable=true)
     */
    private $businessPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="home_phone", type="string", length=255, nullable=true)
     */
    private $homePhone;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_phone", type="string", length=255, nullable=true)
     */
    private $mobilePhone;

    /**
     * @var string
     *
     * @ORM\Column(name="skype", type="string", length=255, nullable=true)
     */
    private $skype;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="persons", fetch="LAZY")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $organization;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="persons", fetch="LAZY")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $user;

    use TimestampableEntity;
    use TenantAwareEntityTrait;
    use Traits\CreatorAwareTrait;
    use Traits\AttachableEntityTrait;

    public function __construct()
    {
        $this->init();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Person
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Person
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set $email
     *
     * @param string $email
     *
     * @return Person
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get $email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set personalEmail
     *
     * @param string $personalEmail
     *
     * @return Person
     */
    public function setPersonalEmail($personalEmail)
    {
        $this->personalEmail = $personalEmail;

        return $this;
    }

    /**
     * Get personalEmail
     *
     * @return string
     */
    public function getPersonalEmail()
    {
        return $this->personalEmail;
    }

    /**
     * Set businessPhone
     *
     * @param string $businessPhone
     *
     * @return Person
     */
    public function setBusinessPhone($businessPhone)
    {
        $this->businessPhone = $businessPhone;

        return $this;
    }

    /**
     * Get businessPhone
     *
     * @return string
     */
    public function getBusinessPhone()
    {
        return $this->businessPhone;
    }

    /**
     * Set homePhone
     *
     * @param string $homePhone
     *
     * @return Person
     */
    public function setHomePhone($homePhone)
    {
        $this->homePhone = $homePhone;

        return $this;
    }

    /**
     * Get homePhone
     *
     * @return string
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * Set mobilePhone
     *
     * @param string $mobilePhone
     *
     * @return Person
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    /**
     * Get mobilePhone
     *
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * Set skype
     *
     * @param string $skype
     *
     * @return Person
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;

        return $this;
    }

    /**
     * Get skype
     *
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * Get the value of Organization
     *
     * @return mixed
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set the value of Organization
     *
     * @param mixed organization
     *
     * @return self
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;

        return $this;
    }
}
