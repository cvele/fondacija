<?php
namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Cvele\MultiTenantBundle\Model\Traits\TenantAwareEntityTrait;
use Cvele\MultiTenantBundle\Model\TenantAwareEntityInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="files")
 * @Gedmo\Uploadable(pathMethod="getPathCallable", callback="callback", filenameGenerator="SHA1", allowOverwrite=false, appendNumber=true)
 */
class File implements TenantAwareEntityInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column
     * @Gedmo\UploadableFilePath
     */
    private $path;

    /**
     * @ORM\Column
     * @Gedmo\UploadableFileName
     */
    private $name;

    /**
     * @ORM\Column
     * @Gedmo\UploadableFileMimeType
     */
    private $mimeType;

    /**
     * @ORM\Column(type="decimal")
     * @Gedmo\UploadableFileSize
     */
    private $size;

    /**
     * @ORM\Column(type="text", name="_text", nullable=true)
     */
    protected $text;

    use TenantAwareEntityTrait;

    public function getPathCallable()
    {
      return "/dev/null";
    }

    public function callback(array $info)
    {
        // Do some stuff with the file..
    }

    /**
     * Set the value of Path
     *
     * @param mixed path
     *
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of Name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of Name
     *
     * @param mixed name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of Mime Type
     *
     * @return mixed
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set the value of Mime Type
     *
     * @param mixed mimeType
     *
     * @return self
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get the value of Size
     *
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the value of Size
     *
     * @param mixed size
     *
     * @return self
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get the value of Text
     *
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set the value of Text
     *
     * @param mixed text
     *
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }


    /**
     * Get the value of Id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get the value of Path
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

}
