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
class User extends BaseUser implements TenantAwareUserInterface, AttachableEntityInterface
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
     * @ORM\Column(type="string", length=140, name="firstname", nullable=false)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", length=140, name="lastname", nullable=false)
     */
    protected $lastname;

    /**
     * @ORM\OneToMany(targetEntity="Person", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $persons;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $files;

    /**
     * @ORM\OneToMany(targetEntity="Organization", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $organizations;

    /**
     * @ORM\OneToOne(targetEntity="Invitation")
     * @ORM\JoinColumn(referencedColumnName="code")
     * @Assert\NotNull(message="Your invitation is wrong", groups={"Registration"})
     */
    protected $invitation;

    /**
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="avatar_file_id", referencedColumnName="id")
     */
    private $avatar;

    public function __construct()
    {
        parent::__construct();
        $this->setupTenantAwareUserTrait();
        $this->files = new ArrayCollection();
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

    /**
     * Get the value of Firstname
     *
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set the value of Firstname
     *
     * @param mixed firstname
     *
     * @return self
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get the value of Lastname
     *
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set the value of Lastname
     *
     * @param mixed lastname
     *
     * @return self
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFullname() {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    public function getDisplayName() {
        return $this->getFirstname() . ' ' . mb_substr($this->getLastname(), 0, 1, 'utf-8');
    }


    /**
     * Get the value of Avatar
     *
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set the value of Avatar
     *
     * @param mixed avatar
     *
     * @return self
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function addFile(File $file)
    {
        throw new \Exception("User entity does not support adding files.");
    }

    public function removeFile(File $file)
    {
        throw new \Exception("User entity does not support removing files.");
    }
}
