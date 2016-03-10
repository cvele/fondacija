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
class Organization implements TenantAwareEntityInterface, AttachableEntityInterface, CreatorAwareInterface
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
     * @ORM\Column(name="name", type="string", length=140)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="logo_file_id", referencedColumnName="id")
     */
    private $logo;

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

    use Traits\CreatorAwareTrait;

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

    /**
     * Get the value of Logo
     *
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set the value of Logo
     *
     * @param mixed logo
     *
     * @return self
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

}
