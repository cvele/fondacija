<?php

namespace AppBundle\Event\Listener;

use Symfony\Component\EventDispatcher\Event;
use Enzim\Lib\TikaWrapper\TikaWrapper;
use AppBundle\Entity\Manager\FileManager;
use AppBundle\Entity\File;
use Liip\ImagineBundle\Model\Binary;

class AvatarAttachedListener
{
    protected $fileManager;
    protected $imagineDataManager;
    protected $imagineFilterManager;
    protected $tokenStorage;

    public function __construct(FileManager $fileManager, $imagineDataManager, $imagineFilterManager, $tokenStorage)
    {
        $this->fileManager = $fileManager;
        $this->imagineDataManager = $imagineDataManager;
        $this->imagineFilterManager = $imagineFilterManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function createAvatarThumbnail(Event $event)
    {
        $file = $event->getEntity();
        $user = $this->tokenStorage->getToken()->getUser();

        if (!($file instanceof File)) {
            return;
        }

        if ($user->getAvatar() !== $file) {
            return;
        }

        $binary = new Binary(
                file_get_contents($file->getPath()),
                $file->getMimeType(),
                'png'
        	  );
        $thumb = $this->imagineFilterManager
                    ->applyFilter($binary, 'avatar')
                    ->getContent();

        $f = fopen($file->getPath(), 'w');
        fwrite($f, $thumb);
        fclose($f);

        $size = filesize($file->getPath());
        $file->setSize($size);
        $this->fileManager->simpleSave($file, 'app.avatar.created');
    }
}
