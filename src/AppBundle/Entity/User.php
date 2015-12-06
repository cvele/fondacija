<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

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

    use TimestampableEntity;

    public function __construct()
    {
        parent::__construct();
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
}