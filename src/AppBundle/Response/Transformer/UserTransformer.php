<?php

namespace AppBundle\Response\Transformer;

use AppBundle\Entity\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'persons', 'organizations', 'files', 'tenants'
    ];

    protected $defaultIncludes = [
        'avatar'
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
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'fullname' => $user->getFullname(),
            'displayname' => $user->getDisplayName()
        ];
    }

    /**
     * Include avatar item
     *
     * @param User $user
     * @return \League\Fractal\Resource\Item
     */
    public function includeAvatar(User $user)
    {
        return $this->item($user->getAvatar(), new FileTransformer);
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
