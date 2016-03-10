<?php

namespace AppBundle\Response\Transformer;

use AppBundle\Entity\File;
use League\Fractal\TransformerAbstract;

class FileTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'user', 'tenant'
    ];

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

    /**
     * Include Tenant
     *
     * @param File $file
     * @return \League\Fractal\Resource\Item
     */
    public function includeTenant(File $file)
    {
        return $this->item($file->getTenant(), new TenantTransformer);
    }

    /**
     * Include User
     *
     * @param File $organization
     * @return \League\Fractal\Resource\Item
     */
    public function includeUser(File $file)
    {
        return $this->item($file->getUser(), new UserTransformer);
    }

}
