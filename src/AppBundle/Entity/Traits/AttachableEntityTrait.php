<?php

namespace AppBundle\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\File;

trait AttachableEntityTrait
{
    /**
     * @ORM\ManyToMany(targetEntity="File", cascade={"remove", "persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $files;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_files", type="integer")
     */
    private $numFiles = 0;

    private function init()
    {
        $this->files = new ArrayCollection();
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function addFile(File $file)
    {
        $this->incrementNumFiles();
        $this->files->add($file);
    }

    public function removeFile(File $file)
    {
        $this->decrementNumFiles();
        $this->files->removeElement($file);
    }

    /**
     * Get the value of Num Files
     *
     * @return integer
     */
    public function getNumFiles()
    {
        return $this->numFiles;
    }

    /**
     * Set the value of Num Files
     *
     * @param integer numFiles
     *
     * @return self
     */
    public function setNumFiles($numFiles)
    {
        $this->numFiles = $numFiles;

        return $this;
    }

    /**
     * Increment value of num files
     *
     * @return self
     */
    public function incrementNumFiles()
    {
        $this->numFiles += 1;

        return $this;
    }

    /**
     * Decrement value of num files
     *
     * @return self
     */
    public function decrementNumFiles()
    {
        $this->numFiles -= 1;

        return $this;
    }
}
