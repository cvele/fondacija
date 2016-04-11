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
    protected $availableIncludes = [
        'user', 'tenant'
    ];

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(File $file = null)
    {
        if ($file === null) {
            return [];
        }

        return [
            'id'        => (int) $file->getId(),
            'mimeType'  => $file->getMimeType(),
            'size'      => $file->getSize(),
            'name'      => $file->getName(),
            'uri'       => $file->getUri()
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
