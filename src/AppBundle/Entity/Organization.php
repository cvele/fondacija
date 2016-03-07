<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Cvele\MultiTenantBundle\Model\Traits\TenantAwareEntityTrait;
use Cvele\MultiTenantBundle\Model\TenantAwareEntityInterface;

/**
 * Organization
 *
 * @ORM\Table(name="organizations")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\OrganizationRepository")
 */
class Organization implements TenantAwareEntityInterface, AttachableEntityInterface
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\OneToMany(targetEntity="Person", mappedBy="organization")
     */
    private $persons;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="organizations")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="File")
     * @ORM\JoinTable(name="organization_files",
     *      joinColumns={@ORM\JoinColumn(name="organization_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $files;

    use TenantAwareEntityTrait;

    use TimestampableEntity;

    public function __construct()
    {
        $this->persons = new ArrayCollection();
        $this->files = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return self
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function addFile(File $file)
    {
        $this->files->add($file);
    }

    public function removeFile(File $file)
    {
        $this->files->removeElement($file);
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

    public function setPersons(array $persons)
    {
        $this->persons = $persons;

        return $this;
    }

    public function addPerson(Person $person)
    {
        $this->persons->add($person);
    }

    public function removePerson(Person $person)
    {
        $this->persons->removeElement($person);
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Gets the value of user.
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the value of user.
     *
     * @param mixed $user the user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
