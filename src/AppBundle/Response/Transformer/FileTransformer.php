<?php

namespace AppBundle\Response\Transformer;

use AppBundle\Entity\File;
use League\Fractal\TransformerAbstract;

class FileTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(File $file)
    {
        return [
            'id'        => (int) $file->getId(),
            'mimeType'  => $file->getMimeType(),
            'size'      => $file->getSize(),
            'name'      => $file->getName(),
            'links' => [
                [
                    'rel' => 'self',
                    'href' => $this->router->generate('app_api_v1_fileapi_read', ['id'=>$file->getId()], true)
                ]
            ]
        ];
    }
}
