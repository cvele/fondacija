<?php

namespace AppBundle\Event\Listener;

use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Entity\Document;
use AppBundle\Entity\DocumentMetadata;
use Doctrine\ORM\EntityManager;
use Enzim\Lib\TikaWrapper\TikaWrapper;
use Cvele\MultiTenantBundle\Helper\TenantHelper;

class UploadListener
{
		protected $em;
		protected $securityTokenStorage;
		protected $tenantHelper;
		protected $kernel_root_dir;

    public function __construct(EntityManager $em, TokenStorageInterface $securityTokenStorage, TenantHelper $tenantHelper, $kernel_root_dir)
    {
			$this->em = $em;
			$this->securityTokenStorage = $securityTokenStorage;
			$this->tenantHelper = $tenantHelper;
			$this->kernel_root_dir = $kernel_root_dir;
    }

    public function onUpload(PostPersistEvent $event)
    {
			$request     = $event->getRequest();
			$title       = $request->request->get('title');
			$description = $request->request->get('description');
			$file        = $event->getFile();
			$user        = $this->securityTokenStorage->getToken()->getUser();

			$uploaded_file = $request->files->get('file');

			$tenant = $this->tenantHelper->getCurrentTenant();

			$file_path = $this->kernel_root_dir . '/../web/uploads/document/'.$file->getFilename();

			$plaintext 			= TikaWrapper::getText($file_path);
			$metadataArray 	= TikaWrapper::getMetaData($file_path);
			$language				= TikaWrapper::getLanguage($file_path);

			$document = new Document();
			$document->setTitle($title);
			$document->setTenant($tenant);
			$document->setDescription($description);
			$document->setFilename($file->getFilename());
			$document->setExtension($file->getExtension());
			$document->setSize($file->getSize());
			$document->setMimeType($file->getMimeType());
			$document->setOriginalFileName($uploaded_file->getClientOriginalName());
			$document->setUser($user);

			$metadata = new DocumentMetadata();
			$metadata->setLanguage($language);
			$metadata->setText($plaintext);
			$metadata->setMetadata($metadataArray);
			$metadata->setDocument($document);

			$this->em->persist($document);
			$this->em->persist($metadata);

	    $this->em->flush();

	    $response = $event->getResponse();

			$response['document_id'] = $document->getId();
			$response['created_at']  = $document->getCreatedAt();
    }
}
