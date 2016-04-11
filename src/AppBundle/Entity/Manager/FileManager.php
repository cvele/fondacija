<?php

namespace AppBundle\Entity\Manager;

use AppBundle\Entity\File;
use AppBundle\Entity\User;
use AppBundle\Entity\Organization;
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

	public function deattach(File $entity, $targetEntity, $eventName = 'app.file.deattached')
	{
		if (!($targetEntity instanceof AttachableEntityInterface)) {
			throw new \Exception("Target entity must implement AttachableEntityInterface.");
		}

		$targetEntity->removeFile($entity);
		$this->om->persist($targetEntity);
		$this->om->flush();
		$this->dispatch($eventName, $entity);
	}

	public function deattachUserAvatar(File $entity, $targetEntity, $eventName = 'app.file.deattached')
	{
		if (!($targetEntity instanceof User)) {
			throw new \Exception("Avatars can only be deattached from users");
		}

		$targetEntity->setAvatar(null);
		$this->om->persist($targetEntity);
		$this->om->flush();
		$this->dispatch($eventName, $entity);
	}

	public function attachUserAvatar(File $entity, $targetEntity, $eventName = 'app.file.attached')
	{
		if (!($targetEntity instanceof User)) {
			throw new \Exception("Avatars can only be attached to users");
		}

		$targetEntity->setAvatar($entity);
		$this->om->persist($targetEntity);
		$this->om->flush();
		$this->dispatch($eventName, $entity);
	}

	public function deattachOrganizationLogo(File $entity, $targetEntity, $eventName = 'app.file.deattached')
	{
		if (!($targetEntity instanceof Organization)) {
			throw new \Exception("Logos can only be attached to organizations");
		}

		$targetEntity->setLogo(null);
		$this->om->persist($targetEntity);
		$this->om->flush();
		$this->dispatch($eventName, $entity);
	}

	public function attachOrganizationLogo(File $entity, $targetEntity, $eventName = 'app.file.attached')
	{
		if (!($targetEntity instanceof Organization)) {
			throw new \Exception("Logos can only be attached to organizations");
		}

		$targetEntity->setLogo($entity);
		$this->om->persist($targetEntity);
		$this->om->flush();
		$this->dispatch($eventName, $entity);
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
	  $this->uploadableManager->markEntityToUpload($entity, $file);
      $this->simpleSave($entity, $eventName);
  }

	public function delete(File $entity, $eventName = 'app.entity.deleted')
	{
		$filePath = $entity->getPath() . DIRECTORY_SEPARATOR . $entity->getName();
		$this->simpleDelete($entity, $eventName);
		unlink($filePath);
	}

}
