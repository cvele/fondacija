<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Cvele\MultiTenantBundle\Model\Traits\TenantAwareEntityTrait;
use Cvele\MultiTenantBundle\Model\TenantAwareEntityInterface;
use FOS\ElasticaBundle\Annotation\Search;

/**
 * Organization
 *
 * @ORM\Table(name="organizations")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\OrganizationRepository")
 * @Search(repositoryClass="AppBundle\Elastica\Repository\OrganizationRepository")
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
     * @var integer
     *
     * @ORM\Column(name="num_persons", type="integer")
     */
    private $numPersons = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Person", mappedBy="organization", fetch="EXTRA_LAZY")
     */
    private $persons;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="organizations", fetch="LAZY")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="File", fetch="EAGER")
     * @ORM\JoinColumn(name="logo_file_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $logo;

    use TenantAwareEntityTrait;

    use TimestampableEntity;

    use Traits\CreatorAwareTrait;

    use Traits\AttachableEntityTrait;

    public function __construct()
    {
        $this->init();
        $this->persons = new ArrayCollection();
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


    /**
     * Get the value of Num Persons
     *
     * @return integer
     */
    public function getNumPersons()
    {
        return $this->numPersons;
    }

    /**
     * Set the value of Num Persons
     *
     * @param integer numPersons
     *
     * @return self
     */
    public function setNumPersons($numPersons)
    {
        $this->numPersons = $numPersons;

        return $this;
    }


    /**
     * Increment value of numPersons
     *
     * @return self
     */
    public function incrementNumPersons()
    {
        $this->numPersons += 1;

        return $this;
    }

    /**
     * Decrement value of numPersons
     *
     * @return self
     */
    public function decrementNumPersons()
    {
        $this->numPersons -= 1;

        return $this;
    }
}
