<?php

namespace AppBundle\Response\Transformer;

use AppBundle\Entity\Person;
use League\Fractal\TransformerAbstract;

class PersonTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tenant', 'user'
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'organization', 'files'
    ];

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Person $person)
    {
        return [
            'id'              => (int) $person->getId(),
            'firstname'       => $person->getFirstname(),
            'lastname'        => $person->getLastname(),
            'email'           => $person->getEmail(),
            'organizationId'  => $person->getOrganization()->getId(),
            'links' => [
                [
                    'rel' => 'self',
                    'href' => $this->router->generate('app_api_v1_personapi_read', ['id'=>$person->getId()], true)
                ]
            ]
        ];
    }

    /**
     * Include File collection
     *
     * @param Person $person
     * @return \League\Fractal\Resource\Collection
     */
    public function includeFiles(Person $person)
    {
        return $this->collection($person->getFiles(), new FileTransformer);
    }

    /**
     * Include Tenant
     *
     * @param Person $person
     * @return \League\Fractal\Resource\Item
     */
    public function includeTenant(Person $person)
    {
        return $this->item($person->getTenant(), new TenantTransformer);
    }

    /**
     * Include Tenant
     *
     * @param User $user
     * @return \League\Fractal\Resource\Item
     */
    public function includeOrganization(Person $person)
    {
        return $this->item($person->getOrganization(), new TenantTransformer);
    }

    /**
     * Include User
     *
     * @param Person $person
     * @return \League\Fractal\Resource\Item
     */
    public function includeUser(Person $person)
    {
        return $this->item($person->getUser(), new UserTransformer);
    }
}
