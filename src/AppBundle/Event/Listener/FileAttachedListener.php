<?php

namespace AppBundle\Event\Listener;

use Symfony\Component\EventDispatcher\Event;
use Enzim\Lib\TikaWrapper\TikaWrapper;
use AppBundle\Entity\Manager\FileManager;
use AppBundle\Entity\File;

class FileAttachedListener
{
    protected $fileManager;

    protected $tikaWrapper;

    public function __construct(FileManager $fileManager, TikaWrapper $tikaWrapper)
    {
        $this->fileManager = $fileManager;
        $this->tikaWrapper = $tikaWrapper;
    }

    public function processApacheTika(Event $event)
    {
        $file = $event->getEntity();

        if (!($file instanceof File)) {
            return;
        }

        $tika = $this->tikaWrapper;
        $plaintext = $tika::getText($file->getPath() . DIRECTORY_SEPARATOR . $file->getName());

        $file->setText($plaintext);
        $this->fileManager->simpleSave($file, 'app.file.tika_indexed');
    }
}
