<?php

namespace AppBundle\Event\Listener;

use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\Security\Core\SecurityContext;
use AppBundle\Entity\Document;
use Doctrine\ORM\EntityManager;

class UploadListener
{
	protected $em;
	protected $securityContext;

    public function __construct(EntityManager $em, SecurityContext $securityContext)
    {
		$this->em              = $em;
		$this->securityContext = $securityContext;
    }

    public function onUpload(PostPersistEvent $event)
    {
		$request     = $event->getRequest();
		$title       = $request->request->get('title');
		$description = $request->request->get('description');
		$file        = $event->getFile();
		$user        = $this->securityContext->getToken()->getUser();

		$uploaded_file = $request->files->get('file');

		$document = new Document();
		$document->setTitle($title);
		$document->setDescription($description);
		$document->setFilename($file->getFilename());
		$document->setExtension($file->getExtension());
		$document->setSize($file->getSize());
		$document->setMimeType($file->getMimeType());
		$document->setOriginalFileName($uploaded_file->getClientOriginalName());
		$document->setUser($user);

		$this->em->persist($document);
        $this->em->flush();

        $response = $event->getResponse();

		$response['document_id'] = $document->getId();
		$response['created_at']  = $document->getCreatedAt();
    }
}