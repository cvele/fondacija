<?php

namespace AppBundle\Entity\Manager;

use AppBundle\Entity\File;
use AppBundle\Entity\Traits\ObjectManagerTrait;
use AppBundle\Entity\AttachableEntityInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{
	use ObjectManagerTrait {
			__construct as traitConstruct;
			save as simpleSave;
			delete as simpleDelete;
	}

	protected $uploadableManager;

	public function __construct(EventDispatcherInterface $dispatcher, ObjectManager $om, $tenantHelper, $class, $uploadableManager)
	{
			$this->traitConstruct($dispatcher, $om, $tenantHelper, $class);
			$this->uploadableManager = $uploadableManager;
	}

	public function attach(File $entity, $targetEntity, $eventName = 'app.file.attached')
	{
		if (!($targetEntity instanceof AttachableEntityInterface)) {
			throw new \Exception("Target entity must implement AttachableEntityInterface.");
		}

		$targetEntity->addFile($entity);
		$this->om->persist($targetEntity);
		$this->om->flush();
		$this->dispatch($eventName, $entity);
	}

  public function save(File $entity, UploadedFile $file, $eventName = 'app.entity.saved')
  {
			$uploadableManager->markEntityToUpload($entity, $file);
      $this->simpleSave($entity, $eventName);
  }

	public function delete(File $entity, $eventName = 'app.entity.deleted')
	{
		$filePath = $entity->getPath() . DIRECTORY_SEPARATOR . $entity->getName();
		$this->simpleDelete($entity, $eventName);
		unlink($filePath);
	}

}
