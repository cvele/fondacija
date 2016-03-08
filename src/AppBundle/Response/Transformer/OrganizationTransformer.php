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
        'persons', 'user', 'tenant'
    ];

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Organization $organization)
    {
        return [
            'id'        => (int) $organization->getId(),
            'name'      => $organization->getName(),
            'description' => $organization->getDescription(),
            'links' => [
                [
                    'rel' => 'self',
                    'href' => $this->router->generate('app_api_v1_organizationapi_read', ['id'=>$organization->getId()], true)
                ]
            ]
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
        $tenant = $organization->getTenant();

        return $this->item($tenant, new TenantTransformer);
    }

    /**
     * Include User
     *
     * @param Organization $organization
     * @return \League\Fractal\Resource\Item
     */
    public function includeUser(Organization $organization)
    {
        $user = $organization->getUser();

        return $this->item($user, new UserTransformer);
    }

    /**
     * Include Persons
     *
     * @param Organization $organization
     * @return \League\Fractal\Resource\Collection
     */
    public function includePersons(Organization $organization)
    {
        $persons = $organization->getPersons();

        return $this->collection($persons, new PersonTransformer);
    }
}
