<?php

namespace AppBundle\Response\Transformer;

use AppBundle\Entity\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tenants'
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'persons', 'organizations', 'files'
    ];

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id'        => (int) $user->getId(),
            'username'  => $user->getUsername(),
            'email'     => $user->getEmail(),
            'lastLogin' => $user->getLastLogin(),
            'enabled'   => (bool) $user->isEnabled(),
            'tenant'    => $user->getTenant(),
            'links' => [
                [
                    'rel' => 'self',
                    'href' => $this->router->generate('app_api_v1_userapi_read', ['id'=>$user->getId()], true)
                ]
            ]
        ];
    }

    /**
     * Include File collection
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includeFiles(User $user)
    {
        return $this->collection($user->getFiles(), new FileTransformer);
    }

    /**
     * Include Organization collection
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includeOrganizations(User $user)
    {
        return $this->collection($user->getOrganizations(), new OrganizationTransformer);
    }

    /**
     * Include Person collection
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includePersons(User $user)
    {
        return $this->collection($user->getPersons(), new PersonTransformer);
    }

    /**
     * Include Tenant
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includeTenants(User $user)
    {
        return $this->collection($user->getUserTenants(), new TenantTransformer);
    }
}
