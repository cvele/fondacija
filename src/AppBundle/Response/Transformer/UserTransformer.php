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
        'tenant'
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
     * Include Tenant
     *
     * @param Organization $user
     * @return \League\Fractal\Resource\Item
     */
    public function includeTenant(User $user)
    {
        $tenant = $user->getTenant();

        return $this->item($tenant, new TenantTransformer);
    }
}
