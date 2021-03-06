<?php

namespace AppBundle\Response\Transformer;

use AppBundle\Entity\Organization;
use League\Fractal\TransformerAbstract;

class OrganizationTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'user', 'tenant', 'logo'
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'persons', 'files'
    ];

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Organization $organization)
    {
        return [
            'id'          => (int) $organization->getId(),
            'name'        => $organization->getName(),
            'description' => $organization->getDescription(),
            'personCount' => $organization->getNumPersons(),
            'fileCount'   => $organization->getNumFiles(),
            'createdAt'   => $organization->getCreatedAt(),
            'updatedAt'   => $organization->getUpdatedAt(),
        ];
    }

    /**
     * Include Tenant
     *
     * @param Organization $organization
     * @return \League\Fractal\Resource\Item
     */
    public function includeTenant(Organization $organization)
    {
        return $this->item($organization->getTenant(), new TenantTransformer);
    }

    /**
     * Include User
     *
     * @param Organization $organization
     * @return \League\Fractal\Resource\Item
     */
    public function includeUser(Organization $organization)
    {
        return $this->item($organization->getUser(), new UserTransformer);
    }

    /**
     * Include Persons
     *
     * @param Organization $organization
     * @return \League\Fractal\Resource\Collection
     */
    public function includePersons(Organization $organization)
    {
        return $this->collection($organization->getPersons(), new PersonTransformer);
    }

    /**
     * Include Logo
     *
     * @param Organization $organization
     * @return \League\Fractal\Resource\Item
     */
    public function includeLogo(Organization $organization)
    {
        return $this->item($organization->getLogo(), new FileTransformer);
    }

    /**
     * Include Files
     *
     * @param Organization $organization
     * @return \League\Fractal\Resource\Collection
     */
    public function includeFiles(Organization $organization)
    {
        return $this->collection($organization->getFiles(), new FileTransformer);
    }
}
