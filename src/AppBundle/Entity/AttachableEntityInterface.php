<?php

namespace AppBundle\Entity;

interface AttachableEntityInterface
{
    public function addFile(File $file);

    public function removeFile(File $file);
}
